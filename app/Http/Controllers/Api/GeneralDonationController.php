<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\GeneralDonationConfirmationMail;
use App\Mail\GeneralDonationReceivedMail;
use App\Models\FundRaisa;
use App\Models\GeneralDonation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class GeneralDonationController extends Controller
{
    protected $stripe;

    private const DONATION_PURPOSES = [
        'Greatest Need',
        'Faculty/staff support',
        'Hafiz Scholarship',
        'Financial aid',
        'HQA Katy deficits',
        'HQA Richmond',
        'Other',
    ];

    /**
     * @return array<string, mixed>
     */
    private function buildSafePayload(Request $request, ?GeneralDonation $donation = null): array
    {
        $payload = $request->except([
            // don't email secrets or internal payment handles
            'client_secret',
            'payment_method',
            'paymentMethod',
            'payment_method_id',
            'paymentMethodId',
        ]);

        // normalize honor fields for email
        if ($request->filled('honorType')) {
            $payload['honorType'] = $request->input('honorType');
        }
        if ($request->filled('honorName')) {
            $payload['honorName'] = $request->input('honorName');
        }
        if ($request->filled('otherPurpose')) {
            $payload['otherPurpose'] = $request->input('otherPurpose');
        }

        if ($donation) {
            $payload = array_merge($payload, [
                'donation_id' => $donation->id,
                'donation_mode' => $donation->donation_mode,
                'frequency' => $donation->frequency,
                'status' => $donation->status,
                'payment_id' => $donation->payment_id,
                'stripe_customer_id' => $donation->stripe_customer_id,
                'stripe_subscription_id' => $donation->stripe_subscription_id,
                'honor_line' => $this->formatHonorLine($donation->honor_type, $donation->honor_name),
                'other_purpose' => $donation->other_purpose,
            ]);
        }

        return $payload;
    }

    private function formatHonorLine(?string $honorType, ?string $honorName): ?string
    {
        $honorType = $honorType ? trim((string) $honorType) : null;
        $honorName = $honorName ? trim((string) $honorName) : null;
        if (empty($honorType) || empty($honorName)) return null;

        return $honorType === 'memory'
            ? 'In the memory of ' . $honorName
            : 'In the honor of ' . $honorName;
    }

    /**
     * @param array<int, array{donation_for:string, other_purpose:?string, amount:int}> $items
     */
    private function buildMetadataPurposes(array $items): string
    {
        $labels = array_map(function (array $item): string {
            $purpose = trim((string) ($item['donation_for'] ?? ''));
            if ($purpose === 'Other') {
                $other = trim((string) ($item['other_purpose'] ?? ''));
                return $other !== '' ? 'Other: ' . $other : 'Other';
            }
            return $purpose !== '' ? $purpose : 'General';
        }, $items);

        return implode(' | ', $labels);
    }

    /**
     * PayPal custom_id max length is limited; keep a safe compact summary.
     *
     * @param array<int, array{donation_for:string, other_purpose:?string, amount:int}> $items
     */
    private function buildPaypalCustomId(array $items): string
    {
        $purposes = $this->buildMetadataPurposes($items);
        $base = 'count=' . count($items) . ';purposes=' . $purposes;
        return mb_substr($base, 0, 120);
    }

    /**
     * Send confirmation to donor and notification to super admins.
     *
     * @param array<string, mixed> $payload
     */
    private function sendDonationEmails(GeneralDonation $donation, array $payload = []): void
    {
        try {
            if (!empty($donation->email)) {
                Mail::to($donation->email)->send(new GeneralDonationConfirmationMail($donation, $payload));
            }

            $superAdmins = User::where('role', 'super_admin')->get();
            $sentToAnyAdmin = false;
            foreach ($superAdmins as $admin) {
                if (!empty($admin->email)) {
                    Mail::to($admin->email)->send(new GeneralDonationReceivedMail($donation, $payload));
                    $sentToAnyAdmin = true;
                }
            }

            // Fallback to configured admin email if there are no super admins.
            if (!$sentToAnyAdmin && !empty(config('mail.admin_email'))) {
                Mail::to(config('mail.admin_email'))->send(new GeneralDonationReceivedMail($donation, $payload));
            }
        } catch (\Throwable $e) {
            // Don't fail the payment flow if mail/queue fails.
            Log::error('Donation email failed (API)', [
                'donation_id' => $donation->id ?? null,
                'donation_mode' => $donation->donation_mode ?? null,
                'frequency' => $donation->frequency ?? null,
                'to_donor' => $donation->email ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    private function normalizeDonationExtras(Request $request): void
    {
        // Accept both camelCase and snake_case from various clients
        if ($request->filled('honor_type') && !$request->filled('honorType')) {
            $request->merge(['honorType' => $request->input('honor_type')]);
        }
        if ($request->filled('honor_name') && !$request->filled('honorName')) {
            $request->merge(['honorName' => $request->input('honor_name')]);
        }
        if ($request->filled('other_purpose') && !$request->filled('otherPurpose')) {
            $request->merge(['otherPurpose' => $request->input('other_purpose')]);
        }
    }

    /**
     * Build one-time donation line items from request.
     *
     * Supports:
     * - Legacy single item fields: amount + donation_for (+ otherPurpose)
     * - Multi item payload: donations[] with amount + donation_for (+ otherPurpose)
     *
     * @return array<int, array{donation_for:string, other_purpose:?string, amount:int}>
     */
    private function buildOneTimeDonationItems(Request $request): array
    {
        $items = [];
        $donations = $request->input('donations');

        if (is_array($donations) && count($donations) > 0) {
            foreach ($donations as $index => $entry) {
                if (!is_array($entry)) {
                    throw ValidationException::withMessages([
                        "donations.$index" => 'Each donation item must be an object.',
                    ]);
                }

                $purpose = trim((string) ($entry['donation_for'] ?? $entry['donationFor'] ?? ''));
                $otherPurposeRaw = $entry['otherPurpose'] ?? $entry['other_purpose'] ?? null;
                $otherPurpose = is_string($otherPurposeRaw) ? trim($otherPurposeRaw) : null;
                $amount = (int) ($entry['amount'] ?? 0);

                if ($purpose === '') {
                    throw ValidationException::withMessages([
                        "donations.$index.donation_for" => 'Donation purpose is required.',
                    ]);
                }

                if ($amount < 1) {
                    throw ValidationException::withMessages([
                        "donations.$index.amount" => 'Amount must be at least 1.',
                    ]);
                }

                if ($purpose === 'Other' && empty($otherPurpose)) {
                    throw ValidationException::withMessages([
                        "donations.$index.otherPurpose" => 'Other purpose is required when donation purpose is Other.',
                    ]);
                }

                $items[] = [
                    'donation_for' => $purpose,
                    'other_purpose' => $purpose === 'Other' ? $otherPurpose : null,
                    'amount' => $amount,
                ];
            }

            return $items;
        }

        $purpose = trim((string) ($request->input('donation_for') ?? ''));
        $otherPurposeRaw = $request->input('otherPurpose');
        $otherPurpose = is_string($otherPurposeRaw) ? trim($otherPurposeRaw) : null;
        $amount = (int) $request->input('amount', 0);

        if ($amount < 1) {
            throw ValidationException::withMessages([
                'amount' => 'Amount must be at least 1.',
            ]);
        }

        if ($purpose === 'Other' && empty($otherPurpose)) {
            throw ValidationException::withMessages([
                'otherPurpose' => 'Other purpose is required when donation purpose is Other.',
            ]);
        }

        $items[] = [
            'donation_for' => $purpose,
            'other_purpose' => $purpose === 'Other' ? $otherPurpose : null,
            'amount' => $amount,
        ];

        return $items;
    }

    /**
     * Build recurring donation line items from request.
     *
     * Supports:
     * - Legacy single item fields: amount + donation_for (+ otherPurpose)
     * - Multi item payload: donations[] with amount + donation_for (+ otherPurpose)
     *
     * @return array<int, array{donation_for:string, other_purpose:?string, amount:int}>
     */
    private function buildRecurringDonationItems(Request $request): array
    {
        $items = [];
        $donations = $request->input('donations');

        if (is_array($donations) && count($donations) > 0) {
            foreach ($donations as $index => $entry) {
                if (!is_array($entry)) {
                    throw ValidationException::withMessages([
                        "donations.$index" => 'Each donation item must be an object.',
                    ]);
                }

                $purpose = trim((string) ($entry['donation_for'] ?? $entry['donationFor'] ?? ''));
                $otherPurposeRaw = $entry['otherPurpose'] ?? $entry['other_purpose'] ?? null;
                $otherPurpose = is_string($otherPurposeRaw) ? trim($otherPurposeRaw) : null;
                $amount = (int) ($entry['amount'] ?? 0);

                if ($amount < 1) {
                    throw ValidationException::withMessages([
                        "donations.$index.amount" => 'Amount must be at least 1.',
                    ]);
                }

                if ($purpose === 'Other' && empty($otherPurpose)) {
                    throw ValidationException::withMessages([
                        "donations.$index.otherPurpose" => 'Other purpose is required when donation purpose is Other.',
                    ]);
                }

                $items[] = [
                    'donation_for' => $purpose,
                    'other_purpose' => $purpose === 'Other' ? $otherPurpose : null,
                    'amount' => $amount,
                ];
            }

            return $items;
        }

        $purpose = trim((string) ($request->input('donation_for') ?? ''));
        $otherPurposeRaw = $request->input('otherPurpose');
        $otherPurpose = is_string($otherPurposeRaw) ? trim($otherPurposeRaw) : null;
        $amount = (int) $request->input('amount', 0);

        if ($amount < 1) {
            throw ValidationException::withMessages([
                'amount' => 'Amount must be at least 1.',
            ]);
        }

        if ($purpose === 'Other' && empty($otherPurpose)) {
            throw ValidationException::withMessages([
                'otherPurpose' => 'Other purpose is required when donation purpose is Other.',
            ]);
        }

        $items[] = [
            'donation_for' => $purpose,
            'other_purpose' => $purpose === 'Other' ? $otherPurpose : null,
            'amount' => $amount,
        ];

        return $items;
    }

    public function show()
    {
        return view('subscribe_dynamic');
    }

    /**
     * Recurring donation (Stripe)
     */
    public function recurringDonation(Request $request)
    {
        $this->normalizeDonationExtras($request);

        // normalize pm key
        $pm =
            $request->input('payment_method') ??
            $request->input('paymentMethod') ??
            $request->input('payment_method_id') ??
            $request->input('paymentMethodId');

        if (!empty($pm) && !$request->filled('payment_method')) {
            $request->merge(['payment_method' => $pm]);
        }

        $request->validate([
            'payment_method' => 'nullable|string',
            'email'          => 'required|email',
            'name'           => 'nullable|string',
            'amount'         => 'required_without:donations|integer|min:1',
            'interval'       => 'required|string|in:month,year',
            'donation_for'   => 'nullable',
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
            'donations'      => 'nullable|array|min:1',
            'donations.*.donation_for' => 'required_with:donations|string',
            'donations.*.amount' => 'required_with:donations|integer|min:1',
            'donations.*.otherPurpose' => 'nullable|string|max:255',
            'donations.*.other_purpose' => 'nullable|string|max:255',
            'honorType'      => 'nullable|string|in:memory,honor',
            'honorName'      => 'nullable|string|max:255|required_with:honorType',
            'address1'       => 'required|string|max:255',
            'address2'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'state'          => 'required|string|max:255',
            'country'        => 'required|string|max:255',
            'customer_id'    => 'nullable|string',
        ]);

        try {
            $items = $this->buildRecurringDonationItems($request);
            $totalAmount = array_sum(array_column($items, 'amount'));
            $primaryPurpose = $items[0]['donation_for'] ?? $request->donation_for;
            $purposes = $this->buildMetadataPurposes($items);

            $paymentMethodId = $request->filled('payment_method') ? $request->payment_method : null;

            // 1) customer (reuse if provided)
            if ($request->filled('customer_id')) {
                $customer = $this->stripe->customers->retrieve($request->customer_id, []);
            } else {
                $customer = $this->stripe->customers->create([
                    'email' => $request->email,
                    'name'  => $request->name,
                    'metadata' => array_filter([
                        'purpose' => $primaryPurpose,
                        'purposes' => $purposes,
                        'donation_count' => (string) count($items),
                    ]),
                ]);
            }

            /**
             * STEP 1 (no pm): return SetupIntent client_secret
             */
            if (empty($paymentMethodId)) {
                $si = $this->stripe->setupIntents->create([
                    'customer' => $customer->id,
                    'usage' => 'off_session',
                    // Let Stripe manage eligible payment method types automatically
                    'automatic_payment_methods' => ['enabled' => true],
                    'excluded_payment_method_types' => ['amazon_pay'],
                    'metadata' => array_filter([
                        'purpose' => $primaryPurpose,
                        'purposes' => $purposes,
                        'donation_count' => (string) count($items),
                        'total_amount' => (string) $totalAmount,
                        'honor_type' => $request->input('honorType'),
                        'honor_name' => $request->input('honorName'),
                        'other_purpose' => $request->input('otherPurpose'),
                    ]),
                ]);

                return response()->json([
                    'paid' => false,
                    'intent_type' => 'setup_intent',
                    'client_secret' => $si->client_secret,  // seti_...
                    'customer_id' => $customer->id,
                    'payment_method_provided' => false,
                    'donations_count' => count($items),
                    'total_amount' => $totalAmount,
                ], 200);
            }

            /**
             * STEP 2 (pm provided): attach + set default
             */
            $this->stripe->paymentMethods->attach($paymentMethodId, [
                'customer' => $customer->id,
            ]);

            $this->stripe->customers->update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            // 2) dynamic product + price for each donation purpose
            $subscriptionItems = [];
            foreach ($items as $item) {
                $label = !empty($item['donation_for']) ? $item['donation_for'] : 'General';
                $product = $this->stripe->products->create([
                    'name' => 'Donation: ' . $label,
                ]);

                $price = $this->stripe->prices->create([
                    'unit_amount' => (int) $item['amount'] * 100,
                    'currency'    => 'usd',
                    'recurring'   => ['interval' => $request->interval],
                    'product'     => $product->id,
                ]);

                $subscriptionItems[] = ['price' => $price->id];
            }

            /**
             * 3) create subscription with expand latest_invoice.payment_intent
             * IMPORTANT: set default_payment_method at subscription level too
             */
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $customer->id,
                'items' => $subscriptionItems,
                'collection_method' => 'charge_automatically',
                'payment_behavior' => 'default_incomplete',
                'default_payment_method' => $paymentMethodId,
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                ],
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => array_filter([
                    'purpose' => $primaryPurpose,
                    'purposes' => $purposes,
                    'donation_count' => (string) count($items),
                    'total_amount' => (string) $totalAmount,
                ]),
            ]);

            $invoice = $subscription->latest_invoice ?? null;
            $pi = $invoice?->payment_intent ?? null;

            // Sometimes PI can be id string
            if (is_string($pi)) {
                $pi = $this->stripe->paymentIntents->retrieve($pi);
            }

            // Try to pay invoice server-side (helps when no SCA is needed)
            // If SCA is needed, Stripe will still require frontend confirmPayment.
            $invoiceId = $invoice?->id ?? null;
            if ($invoiceId && ($invoice->status ?? null) === 'open') {
                try {
                    $invoicePaid = $this->stripe->invoices->pay($invoiceId, [
                        'payment_method' => $paymentMethodId,
                        'expand' => ['payment_intent'],
                    ]);
                    $invoice = $invoicePaid;
                    $pi = $invoice->payment_intent ?? $pi;
                    if (is_string($pi)) $pi = $this->stripe->paymentIntents->retrieve($pi);
                } catch (\Exception $ex) {
                    // ignore: if requires_action, frontend will handle using pi client_secret
                }
            }

            $clientSecret = $pi?->client_secret ?? null;
            $piStatus = $pi?->status ?? null;

            $paid =
                ($subscription->status === 'active' || $subscription->status === 'trialing') ||
                (($invoice->paid ?? false) === true) ||
                ($piStatus === 'succeeded');

            // save one donation record per selected purpose
            $goal = FundRaisa::latest()->first();
            $saved = [];
            foreach ($items as $item) {
                $saved[] = GeneralDonation::create([
                    'fund_raisa_id'          => $goal?->id,
                    'donation_for'           => $item['donation_for'],
                    'other_purpose'          => $item['other_purpose'],
                    'honor_type'             => $request->input('honorType'),
                    'honor_name'             => $request->input('honorName'),
                    'name'                   => $request->name,
                    'email'                  => $request->email,
                    'amount'                 => (int) $item['amount'],
                    'payment_id'             => $subscription->id,
                    'donation_mode'          => 'stripe',
                    'frequency'              => $request->interval,
                    'stripe_customer_id'     => $customer->id,
                    'stripe_subscription_id' => $subscription->id,
                    'status'                 => $paid ? 'paid' : 'pending',
                    'address1'               => $request->address1,
                    'address2'               => $request->address2,
                    'city'                   => $request->city,
                    'state'                  => $request->state,
                    'country'                => $request->country,
                ]);
            }
            $primaryDonation = $saved[0];

            if ($paid) {
                $payload = $this->buildSafePayload($request, $primaryDonation);
                $payload['donations'] = $items;
                $payload['donations_count'] = count($items);
                $payload['total_amount'] = $totalAmount;
                $this->sendDonationEmails($primaryDonation, $payload);
            }

            return response()->json([
                'paid' => $paid,
                'donation_id' => $primaryDonation->id,
                'donation_ids' => array_map(fn($d) => $d->id, $saved),
                'donations_count' => count($saved),
                'total_amount' => $totalAmount,
                'customer_id' => $customer->id,

                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status,

                'invoice_id' => $invoiceId,
                'invoice_status' => $invoice?->status,
                'invoice_paid' => (bool)($invoice->paid ?? false),

                // ✅ IMPORTANT: frontend will confirm this if requires_action
                'intent_type' => $clientSecret ? 'payment_intent' : null,
                'client_secret' => $clientSecret, // pi_...
                'pi_status' => $piStatus,

                'payment_method_provided' => true,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Stripe Recurring Error: ' . $e->getMessage());
            return response()->json(['paid' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function confirmOneTimeDonation(Request $request)
    {
        $this->normalizeDonationExtras($request);

        $request->validate([
            'payment_intent_id' => 'required|string',

            // We re-send details so we can store address etc
            'email'          => 'required|email',
            'name'           => 'required|string',
            'amount'         => 'required_without:donations|integer|min:1',

            'donation_for'   => 'nullable',
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
            'donations'      => 'nullable|array|min:1',
            'donations.*.donation_for' => 'required_with:donations|string',
            'donations.*.amount' => 'required_with:donations|integer|min:1',
            'donations.*.otherPurpose' => 'nullable|string|max:255',
            'donations.*.other_purpose' => 'nullable|string|max:255',

            'honorType'      => 'nullable|string|in:memory,honor',
            'honorName'      => 'nullable|string|max:255|required_with:honorType',

            'address1'       => 'required|string',
            'address2'       => 'nullable|string',
            'city'           => 'required|string',
            'state'          => 'required|string',
            'country'        => 'required|string',
        ]);

        try {
            $items = $this->buildOneTimeDonationItems($request);
            $totalAmount = array_sum(array_column($items, 'amount'));

            $pi = $this->stripe->paymentIntents->retrieve($request->payment_intent_id, []);

            // ✅ Must be succeeded
            if (($pi->status ?? null) !== 'succeeded') {
                return response()->json([
                    'paid' => false,
                    'message' => 'Payment not completed.',
                    'pi_status' => $pi->status ?? null,
                ], 400);
            }

            // ✅ Basic anti-tamper checks
            $piAmount = (int)($pi->amount ?? 0) / 100;
            if ((int)$piAmount !== (int)$totalAmount) {
                return response()->json([
                    'paid' => false,
                    'message' => 'Amount mismatch.',
                ], 400);
            }

            if (!empty($pi->receipt_email) && strtolower($pi->receipt_email) !== strtolower($request->email)) {
                return response()->json([
                    'paid' => false,
                    'message' => 'Email mismatch.',
                ], 400);
            }

            // ✅ Idempotency: don’t create duplicates
            $existing = GeneralDonation::where('payment_id', $pi->id)->first();
            if ($existing) {
                return response()->json([
                    'paid' => true,
                    'donation_id' => $existing->id,
                    'message' => 'Already saved.',
                ], 200);
            }

            $goal = FundRaisa::latest()->first();

            $saved = [];
            foreach ($items as $item) {
                $saved[] = GeneralDonation::create([
                    'fund_raisa_id'      => $goal?->id,
                    'donation_for'       => $item['donation_for'],
                    'other_purpose'      => $item['other_purpose'],
                    'honor_type'         => $request->input('honorType'),
                    'honor_name'         => $request->input('honorName'),
                    'name'               => $request->name,
                    'email'              => $request->email,
                    'amount'             => (int)$item['amount'],
                    'payment_id'         => $pi->id,
                    'donation_mode'      => 'stripe',
                    'frequency'          => 'one_time',
                    'stripe_customer_id' => $pi->customer ?? null,
                    'status'             => 'paid',
                    'address1'           => $request->address1,
                    'address2'           => $request->address2,
                    'city'               => $request->city,
                    'state'              => $request->state,
                    'country'            => $request->country,
                ]);
            }

            $primaryDonation = $saved[0];
            $payload = $this->buildSafePayload($request, $primaryDonation);
            $payload['donations'] = $items;
            $payload['donations_count'] = count($items);
            $payload['total_amount'] = $totalAmount;
            $this->sendDonationEmails($primaryDonation, $payload);

            return response()->json([
                'paid' => true,
                'donation_id' => $primaryDonation->id,
                'donation_ids' => array_map(fn($d) => $d->id, $saved),
                'donations_count' => count($saved),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Stripe Confirm OneTime Error: ' . $e->getMessage());
            return response()->json(['paid' => false, 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * One-time donation (Stripe)
     */
    public function oneTimeDonation(Request $request)
    {
        $this->normalizeDonationExtras($request);

        // Normalize payment method key (optional legacy)
        $pm =
            $request->input('payment_method') ??
            $request->input('paymentMethod') ??
            $request->input('payment_method_id') ??
            $request->input('paymentMethodId');

        if (!empty($pm) && !$request->filled('payment_method')) {
            $request->merge(['payment_method' => $pm]);
        }

        $request->validate([
            'payment_method' => 'nullable|string',
            'email'          => 'required|email',
            'name'           => 'required|string',
            'amount'         => 'required_without:donations|integer|min:1',

            'donation_for'   => 'nullable',
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
            'donations'      => 'nullable|array|min:1',
            'donations.*.donation_for' => 'required_with:donations|string',
            'donations.*.amount' => 'required_with:donations|integer|min:1',
            'donations.*.otherPurpose' => 'nullable|string|max:255',
            'donations.*.other_purpose' => 'nullable|string|max:255',

            'honorType'      => 'nullable|string|in:memory,honor',
            'honorName'      => 'nullable|string|max:255|required_with:honorType',

            'address1'       => 'required|string',
            'address2'       => 'nullable|string',
            'city'           => 'required|string',
            'state'          => 'required|string',
            'country'        => 'required|string',
        ]);

        try {
            $items = $this->buildOneTimeDonationItems($request);
            $totalAmount = array_sum(array_column($items, 'amount'));
            $isMulti = count($items) > 1;
            $purposes = $this->buildMetadataPurposes($items);

            $customer = $this->stripe->customers->create([
                'email' => $request->email,
                'name'  => $request->name,
            ]);

            $primaryPurpose = $items[0]['donation_for'] ?? 'General';

            $piPayload = [
                'amount'   => (int)$totalAmount * 100,
                'currency' => 'usd',
                'customer' => $customer->id,
                'receipt_email' => $request->email,
                'description'   => $isMulti
                    ? 'One-time Donation (Multiple Purposes)'
                    : 'One-time Donation: ' . $primaryPurpose,

                // ✅ Save minimal metadata (don’t store address here)
                'metadata' => array_filter([
                    'type'          => 'general_donation_one_time',
                    'purpose'       => $primaryPurpose,
                    'purposes'      => $purposes,
                    'other_purpose' => $items[0]['other_purpose'] ?? null,
                    'donation_count' => (string) count($items),
                    'honor_type'    => $request->input('honorType'),
                    'honor_name'    => $request->input('honorName'),
                ]),

                'automatic_payment_methods' => ['enabled' => true],
            ];

            // Optional legacy server-side confirm if payment_method passed
            if ($request->filled('payment_method')) {
                $piPayload['payment_method'] = $request->payment_method;
                $piPayload['confirm'] = true;
                $piPayload['off_session'] = false;
                $piPayload['automatic_payment_methods'] = ['enabled' => true, 'allow_redirects' => 'never'];
            }

            $pi = $this->stripe->paymentIntents->create($piPayload);

            // ✅ IMPORTANT: DO NOT create donation record here if not succeeded
            if ($pi->status === 'succeeded') {
                // If server-side confirm succeeded, you may create record immediately
                $goal = FundRaisa::latest()->first();

                $saved = [];
                foreach ($items as $item) {
                    $saved[] = GeneralDonation::create([
                        'fund_raisa_id'      => $goal?->id,
                        'donation_for'       => $item['donation_for'],
                        'other_purpose'      => $item['other_purpose'],
                        'honor_type'         => $request->input('honorType'),
                        'honor_name'         => $request->input('honorName'),
                        'name'               => $request->name,
                        'email'              => $request->email,
                        'amount'             => (int)$item['amount'],
                        'payment_id'         => $pi->id,
                        'donation_mode'      => 'stripe',
                        'frequency'          => 'one_time',
                        'stripe_customer_id' => $customer->id,
                        'status'             => 'paid',
                        'address1'           => $request->address1,
                        'address2'           => $request->address2,
                        'city'               => $request->city,
                        'state'              => $request->state,
                        'country'            => $request->country,
                    ]);
                }

                $primaryDonation = $saved[0];
                $payload = $this->buildSafePayload($request, $primaryDonation);
                $payload['donations'] = $items;
                $payload['donations_count'] = count($items);
                $payload['total_amount'] = $totalAmount;
                $this->sendDonationEmails($primaryDonation, $payload);

                return response()->json([
                    'paid' => true,
                    'donation_id' => $primaryDonation->id,
                    'donation_ids' => array_map(fn($d) => $d->id, $saved),
                    'donations_count' => count($saved),
                    'total_amount' => $totalAmount,
                    'payment_intent_id' => $pi->id,
                    'pi_status' => $pi->status,
                ], 200);
            }

            // ✅ Normal PaymentElement flow: return PI secret, frontend confirms
            return response()->json([
                'paid' => false,
                'payment_intent_id' => $pi->id,
                'client_secret' => $pi->client_secret,
                'customer_id' => $customer->id,
                'pi_status' => $pi->status,
                'donations_count' => count($items),
                'total_amount' => $totalAmount,
            ], 200);
        } catch (Exception $e) {
            Log::error('Stripe OneTime Error: ' . $e->getMessage());
            return response()->json(['paid' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create PayPal Order
     */
    public function createPaypalOrder(Request $request)
    {
        $this->normalizeDonationExtras($request);

        $request->validate([
            'amount'         => 'required_without:donations|numeric|min:1',
            'donation_for'   => 'nullable|string',
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
            'donations'      => 'nullable|array|min:1',
            'donations.*.donation_for' => 'required_with:donations|string',
            'donations.*.amount' => 'required_with:donations|integer|min:1',
            'donations.*.otherPurpose' => 'nullable|string|max:255',
            'donations.*.other_purpose' => 'nullable|string|max:255',
        ]);

        try {
            $items = $this->buildOneTimeDonationItems($request);
            $totalAmount = array_sum(array_column($items, 'amount'));
            $purposes = $this->buildMetadataPurposes($items);
            $paypalCustomId = $this->buildPaypalCustomId($items);

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => number_format($totalAmount, 2, '.', '')
                    ],
                    "custom_id" => $paypalCustomId,
                    "description" => count($items) > 1
                        ? ("General Donation: " . mb_substr($purposes, 0, 90))
                        : ("General Donation: " . $purposes),
                ]],
                "application_context" => [
                    "shipping_preference" => "NO_SHIPPING",
                    "user_action" => "PAY_NOW"
                ]
            ]);

            return response()->json([
                ...$response,
                'donations_count' => count($items),
                'total_amount' => $totalAmount,
                'purposes' => $purposes,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Capture PayPal Order
     */
    public function capturePaypalOrder(Request $request)
    {
        $this->normalizeDonationExtras($request);

        $request->validate([
            'orderID'      => 'required',
            'email'        => 'required|email',
            'name'         => 'required|string',
            'amount'       => 'required_without:donations|numeric|min:1',
            'donation_for' => 'nullable|string',
            'otherPurpose' => 'nullable|string|max:255|required_if:donation_for,Other',
            'donations'      => 'nullable|array|min:1',
            'donations.*.donation_for' => 'required_with:donations|string',
            'donations.*.amount' => 'required_with:donations|integer|min:1',
            'donations.*.otherPurpose' => 'nullable|string|max:255',
            'donations.*.other_purpose' => 'nullable|string|max:255',
            'honorType'    => 'nullable|string|in:memory,honor',
            'honorName'    => 'nullable|string|max:255|required_with:honorType',
            'address1'     => 'required|string',
            'city'         => 'required|string',
            'state'        => 'required|string',
            'country'      => 'required|string',
        ]);

        try {
            $items = $this->buildOneTimeDonationItems($request);
            $totalAmount = array_sum(array_column($items, 'amount'));

            // Idempotency: don't create duplicates if client retries save call.
            $existingRows = GeneralDonation::where('payment_id', $request->orderID)->get();
            if ($existingRows->isNotEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Already saved.',
                    'donation_id' => $existingRows->first()->id,
                    'donation_ids' => $existingRows->pluck('id')->values(),
                    'donations_count' => $existingRows->count(),
                ]);
            }

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->capturePaymentOrder($request->orderID);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                // Basic anti-tamper check.
                $capturedValue = (float) (
                    $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0
                );
                if (abs($capturedValue - (float) $totalAmount) > 0.0001) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Amount mismatch.',
                    ], 400);
                }

                $goal = FundRaisa::latest()->first();
                $saved = [];
                foreach ($items as $item) {
                    $saved[] = GeneralDonation::create([
                        'fund_raisa_id'   => $goal?->id,
                        'donation_for'    => $item['donation_for'],
                        'other_purpose'   => $item['other_purpose'],
                        'honor_type'      => $request->input('honorType'),
                        'honor_name'      => $request->input('honorName'),
                        'name'            => $request->name,
                        'email'           => $request->email,
                        'amount'          => (int) $item['amount'],
                        'payment_id'      => $request->orderID,
                        'donation_mode'   => 'paypal',
                        'frequency'       => 'one_time',
                        'status'          => 'paid',
                        'address1'        => $request->address1,
                        'address2'        => $request->address2,
                        'city'            => $request->city,
                        'state'           => $request->state,
                        'country'         => $request->country,
                    ]);
                }

                $primaryDonation = $saved[0];
                $payload = $this->buildSafePayload($request, $primaryDonation);
                $payload['donations'] = $items;
                $payload['donations_count'] = count($items);
                $payload['total_amount'] = $totalAmount;
                $this->sendDonationEmails($primaryDonation, $payload);

                return response()->json([
                    'status' => 'success',
                    'donation' => $primaryDonation,
                    'donation_ids' => array_map(fn($d) => $d->id, $saved),
                    'donations_count' => count($saved),
                    'total_amount' => $totalAmount,
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'PayPal capture failed'], 400);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Create PayPal Subscription (Recurring)
     */
    public function createPaypalSubscription(Request $request)
    {
        $this->normalizeDonationExtras($request);

        $request->validate([
            'amount'       => 'required_without:donations|numeric|min:1',
            'interval'     => 'required|string|in:month,year',
            'donation_for' => 'nullable',
            'otherPurpose' => 'nullable|string|max:255|required_if:donation_for,Other',
            'donations'      => 'nullable|array|min:1',
            'donations.*.donation_for' => 'required_with:donations|string',
            'donations.*.amount' => 'required_with:donations|integer|min:1',
            'donations.*.otherPurpose' => 'nullable|string|max:255',
            'donations.*.other_purpose' => 'nullable|string|max:255',
            'honorType'    => 'nullable|string|in:memory,honor',
            'honorName'    => 'nullable|string|max:255|required_with:honorType',
            'name'         => 'required|string',
            'email'        => 'required|email',
            'return_url'   => 'nullable|url',
            'cancel_url'   => 'nullable|url',
            // Include address validation if you want to save it before the redirect
        ]);

        try {
            $items = $this->buildRecurringDonationItems($request);
            $totalAmount = array_sum(array_column($items, 'amount'));
            $primaryPurpose = $items[0]['donation_for'] ?? 'General Donation';
            $purposes = $this->buildMetadataPurposes($items);
            $paypalCustomId = $this->buildPaypalCustomId($items);

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->setCurrency(config('paypal.currency', 'USD'));
            $provider->getAccessToken();

            // 1. DYNAMIC PRODUCT CREATION
            $productData = [
                "name" => "Donation Service",
                "description" => "Recurring donation for " . config('app.name'),
                "type" => "SERVICE",
                // PayPal Catalog Products API expects a fixed enum value (e.g. CHARITY/NONPROFIT/SOFTWARE).
                "category" => "CHARITY"
            ];

            // Avoid creating duplicate products by using a consistent Request-ID
            $request_id = 'PRODUCT-' . md5('donation-service-fixed');
            $product = $provider->createProduct($productData, $request_id);
            if (isset($product['error'])) {
                throw new Exception("PayPal Product Error: " . json_encode($product));
            }
            $productId = $product['id'] ?? null;
            if (!$productId) {
                throw new Exception("PayPal Product Error: Missing product id. Response: " . json_encode($product));
            }

            // 2. CREATE DYNAMIC PLAN
            $planName = count($items) > 1
                ? "Donation: Multiple Purposes ($" . $totalAmount . ")"
                : "Donation: " . $primaryPurpose . " ($" . $totalAmount . ")";
            $planDetails = [
                "product_id" => $productId,
                "name" => $planName,
                "description" => mb_substr($planName . " | " . $purposes, 0, 127),
                "status" => "ACTIVE",
                "billing_cycles" => [
                    [
                        "frequency" => [
                            "interval_unit" => strtoupper($request->interval == 'month' ? 'MONTH' : 'YEAR'),
                            "interval_count" => 1
                        ],
                        "tenure_type" => "REGULAR",
                        "sequence" => 1,
                        "total_cycles" => 0, // Infinite until canceled
                        "pricing_scheme" => [
                            "fixed_price" => [
                                "value" => number_format($totalAmount, 2, '.', ''),
                                "currency_code" => "USD"
                            ]
                        ]
                    ]
                ],
                "payment_preferences" => [
                    "auto_bill_outstanding" => true,
                    "setup_fee" => ["value" => "0", "currency_code" => "USD"],
                    "setup_fee_failure_action" => "CONTINUE",
                    "payment_failure_threshold" => 3
                ]
            ];

            $plan = $provider->createPlan($planDetails);

            if (isset($plan['error'])) {
                throw new Exception("PayPal Plan Error: " . json_encode($plan));
            }
            if (empty($plan['id'])) {
                throw new Exception("PayPal Plan Error: Missing plan id. Response: " . json_encode($plan));
            }

            // 3. CREATE SUBSCRIPTION
            $defaultReturnUrl = url('/subscribe?paypal=success');
            $defaultCancelUrl = url('/subscribe?paypal=cancel');
            $returnUrl = $request->input('return_url') ?: $defaultReturnUrl;
            $cancelUrl = $request->input('cancel_url') ?: $defaultCancelUrl;

            // Split full name into given/surname (best-effort)
            $nameParts = preg_split('/\s+/', trim((string) $request->name)) ?: [];
            $givenName = $nameParts[0] ?? 'Donor';
            $surname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : ' ';

            $subscriptionData = [
                "plan_id" => $plan['id'],
                "custom_id" => $paypalCustomId,
                "subscriber" => [
                    "name" => [
                        "given_name" => $givenName,
                        "surname" => $surname,
                    ],
                    "email_address" => $request->email,
                ],
                "application_context" => [
                    "brand_name" => config('app.name'),
                    "locale" => "en-US",
                    // Let PayPal decide whether to show wallet vs card (if eligible).
                    // Subscriptions often still require PayPal login, depending on account/region.
                    "landing_page" => "NO_PREFERENCE",
                    "shipping_preference" => "NO_SHIPPING",
                    "user_action" => "SUBSCRIBE_NOW",
                    "return_url" => $returnUrl,
                    "cancel_url" => $cancelUrl,
                ]
            ];

            $subscription = $provider->createSubscription($subscriptionData);

            if (isset($subscription['error'])) {
                throw new Exception("PayPal Subscription Error: " . json_encode($subscription));
            }

            // 4. OPTIONAL: Save a pending record
            // Since PayPal subscriptions are approved on the frontend, 
            // you might want to save the record here as 'pending' 
            // and update it later via Webhook or a 'complete' endpoint.

            return response()->json([
                ...$subscription,
                'donations_count' => count($items),
                'total_amount' => $totalAmount,
                'purposes' => $purposes,
            ]);
        } catch (Exception $e) {
            Log::error('PayPal Subscription Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create subscription',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm and Save PayPal Subscription (Call this after Frontend success)
     */
    public function savePaypalSubscription(Request $request)
    {
        $this->normalizeDonationExtras($request);

        $request->validate([
            'subscriptionID' => 'required',
            'amount'         => 'required_without:donations|numeric|min:1',
            'email'          => 'required|email',
            'donation_for'   => 'nullable',
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
            'donations'      => 'nullable|array|min:1',
            'donations.*.donation_for' => 'required_with:donations|string',
            'donations.*.amount' => 'required_with:donations|integer|min:1',
            'donations.*.otherPurpose' => 'nullable|string|max:255',
            'donations.*.other_purpose' => 'nullable|string|max:255',
            'honorType'      => 'nullable|string|in:memory,honor',
            'honorName'      => 'nullable|string|max:255|required_with:honorType',
            'interval'       => 'required',
        ]);

        $items = $this->buildRecurringDonationItems($request);
        $totalAmount = array_sum(array_column($items, 'amount'));

        // Idempotency: avoid duplicate rows for same PayPal subscription.
        $existingRows = GeneralDonation::where('payment_id', $request->subscriptionID)->get();
        if ($existingRows->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Already saved.',
                'donation_id' => $existingRows->first()->id,
                'donation_ids' => $existingRows->pluck('id')->values(),
                'donations_count' => $existingRows->count(),
            ]);
        }

        $goal = FundRaisa::latest()->first();

        $saved = [];
        foreach ($items as $item) {
            $saved[] = GeneralDonation::create([
                'fund_raisa_id'   => $goal?->id,
                'donation_for'    => $item['donation_for'],
                'other_purpose'   => $item['other_purpose'],
                'honor_type'      => $request->input('honorType'),
                'honor_name'      => $request->input('honorName'),
                'name'            => $request->name,
                'email'           => $request->email,
                'amount'          => (int) $item['amount'],
                'payment_id'      => $request->subscriptionID,
                'donation_mode'   => 'paypal',
                'frequency'       => $request->interval, // 'month' or 'year'
                'status'          => 'paid', // Or 'active'
                'address1'        => $request->address1,
                'city'            => $request->city,
                'state'           => $request->state,
                'country'         => $request->country,
            ]);
        }

        $primaryDonation = $saved[0];
        $payload = $this->buildSafePayload($request, $primaryDonation);
        $payload['donations'] = $items;
        $payload['donations_count'] = count($items);
        $payload['total_amount'] = $totalAmount;
        $this->sendDonationEmails($primaryDonation, $payload);

        return response()->json([
            'status' => 'success',
            'donation' => $primaryDonation,
            'donation_ids' => array_map(fn($d) => $d->id, $saved),
            'donations_count' => count($saved),
            'total_amount' => $totalAmount,
        ]);
    }
}
