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
use Illuminate\Validation\Rule;
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
     * Send confirmation to donor and notification to super admins.
     *
     * @param array<string, mixed> $payload
     */
    private function sendDonationEmails(GeneralDonation $donation, array $payload = []): void
    {
        try {
            if (!empty($donation->email)) {
                Mail::to($donation->email)->queue(new GeneralDonationConfirmationMail($donation, $payload));
            }

            $superAdmins = User::where('role', 'super_admin')->get();
            $sentToAnyAdmin = false;
            foreach ($superAdmins as $admin) {
                if (!empty($admin->email)) {
                    Mail::to($admin->email)->queue(new GeneralDonationReceivedMail($donation, $payload));
                    $sentToAnyAdmin = true;
                }
            }

            // Fallback to configured admin email if there are no super admins.
            if (!$sentToAnyAdmin && !empty(config('mail.admin_email'))) {
                Mail::to(config('mail.admin_email'))->queue(new GeneralDonationReceivedMail($donation, $payload));
            }
        } catch (\Throwable $e) {
            // Don't fail the payment flow if mail/queue fails.
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
            'amount'         => 'required|integer|min:1',
            'interval'       => 'required|string|in:month,year',
            'donation_for'   => ['required', 'string', 'max:255', Rule::in(self::DONATION_PURPOSES)],
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
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
            $paymentMethodId = $request->filled('payment_method') ? $request->payment_method : null;

            // 1) customer (reuse if provided)
            if ($request->filled('customer_id')) {
                $customer = $this->stripe->customers->retrieve($request->customer_id, []);
            } else {
                $customer = $this->stripe->customers->create([
                    'email' => $request->email,
                    'name'  => $request->name,
                    'metadata' => ['purpose' => $request->donation_for],
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
                        'purpose' => $request->donation_for,
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

            // 2) product + price (ok to keep dynamic)
            $product = $this->stripe->products->create([
                'name' => 'Donation: ' . $request->donation_for,
            ]);

            $price = $this->stripe->prices->create([
                'unit_amount' => (int) $request->amount * 100,
                'currency'    => 'usd',
                'recurring'   => ['interval' => $request->interval],
                'product'     => $product->id,
            ]);

            /**
             * 3) create subscription with expand latest_invoice.payment_intent
             * IMPORTANT: set default_payment_method at subscription level too
             */
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'collection_method' => 'charge_automatically',
                'payment_behavior' => 'default_incomplete',
                'default_payment_method' => $paymentMethodId,
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                ],
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => ['purpose' => $request->donation_for],
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

            // save donation record (optional - keep your model logic)
            $goal = FundRaisa::latest()->first();
            $donation = GeneralDonation::create([
                'fund_raisa_id'          => $goal?->id,
                'donation_for'           => $request->donation_for,
                'other_purpose'          => $request->donation_for === 'Other' ? $request->input('otherPurpose') : null,
                'honor_type'             => $request->input('honorType'),
                'honor_name'             => $request->input('honorName'),
                'name'                   => $request->name,
                'email'                  => $request->email,
                'amount'                 => (int) $request->amount,
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

            if ($paid) {
                $this->sendDonationEmails($donation, $this->buildSafePayload($request, $donation));
            }

            return response()->json([
                'paid' => $paid,
                'donation_id' => $donation->id,
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

    /**
     * One-time donation (Stripe)
     */
    public function oneTimeDonation(Request $request)
    {
        $this->normalizeDonationExtras($request);

        // Normalize payment method key (frontend may send different names)
        $pm =
            $request->input('payment_method') ??
            $request->input('paymentMethod') ??
            $request->input('payment_method_id') ??
            $request->input('paymentMethodId');
        if (!empty($pm) && !$request->filled('payment_method')) {
            $request->merge(['payment_method' => $pm]);
        }

        $request->validate([
            // For PaymentElement flows, payment_method is created on frontend during confirmPayment.
            // For legacy flows, allow passing a payment_method id (pm_*) and we will confirm server-side.
            'payment_method' => 'nullable|string',
            'email'          => 'required|email',
            'amount'         => 'required|integer|min:1',
            'donation_for'   => ['required', 'string', Rule::in(self::DONATION_PURPOSES)],
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
            'honorType'      => 'nullable|string|in:memory,honor',
            'honorName'      => 'nullable|string|max:255|required_with:honorType',
            'address1'       => 'required|string',
            'city'           => 'required|string',
            'state'          => 'required|string',
            'country'        => 'required|string',
        ]);

        try {
            $customer = $this->stripe->customers->create([
                'email' => $request->email,
                'name'  => $request->name,
            ]);

            // Create PI without confirming so PaymentElement can confirm (wallets/3DS)
            $piPayload = [
                'amount'   => (int) $request->amount * 100,
                'currency' => 'usd',
                'customer' => $customer->id,
                'receipt_email' => $request->email,
                'description'   => 'One-time Donation: ' . $request->donation_for,
                'metadata' => [
                    'type' => 'general_donation_one_time',
                    'purpose' => $request->donation_for,
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ];

            // Legacy: if payment_method provided, confirm server-side (may still require_action)
            if ($request->filled('payment_method')) {
                $piPayload['payment_method'] = $request->payment_method;
                $piPayload['confirm'] = true;
                $piPayload['off_session'] = false;
                $piPayload['automatic_payment_methods'] = ['enabled' => true, 'allow_redirects' => 'never'];
            }

            $pi = $this->stripe->paymentIntents->create($piPayload);

            $goal = FundRaisa::latest()->first();

            $donation = GeneralDonation::create([
                'fund_raisa_id'      => $goal?->id,
                'donation_for'       => $request->donation_for,
                'other_purpose'      => $request->donation_for === 'Other' ? $request->input('otherPurpose') : null,
                'honor_type'         => $request->input('honorType'),
                'honor_name'         => $request->input('honorName'),
                'name'               => $request->name,
                'email'              => $request->email,
                'amount'             => (int) $request->amount,
                'payment_id'         => $pi->id,
                'donation_mode'      => 'stripe',
                'frequency'          => 'one_time',
                'stripe_customer_id' => $customer->id,
                'status'             => ($pi->status === 'succeeded') ? 'paid' : 'pending',
                'address1'           => $request->address1,
                'address2'           => $request->address2,
                'city'               => $request->city,
                'state'              => $request->state,
                'country'            => $request->country,
            ]);

            if ($pi->status === 'succeeded') {
                $this->sendDonationEmails($donation, $this->buildSafePayload($request, $donation));
            }

            // If not yet succeeded, frontend should confirm with client_secret
            if ($pi->status !== 'succeeded' && !empty($pi->client_secret)) {
                return response()->json([
                    'paid' => false,
                    'donation_id' => $donation->id,
                    'payment_intent_id' => $pi->id,
                    'client_secret' => $pi->client_secret,
                    'pi_status' => $pi->status,
                ], 200);
            }

            return response()->json([
                'paid' => ($pi->status === 'succeeded'),
                'donation_id' => $donation->id,
                'payment_intent_id' => $pi->id,
                'pi_status' => $pi->status,
            ]);
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
        $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => number_format($request->amount, 2, '.', '')
                    ]
                ]],
                "application_context" => [
                    "shipping_preference" => "NO_SHIPPING",
                    "user_action" => "PAY_NOW"
                ]
            ]);

            return response()->json($response);
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
            'amount'       => 'required|numeric',
            'donation_for' => ['required', 'string', Rule::in(self::DONATION_PURPOSES)],
            'otherPurpose' => 'nullable|string|max:255|required_if:donation_for,Other',
            'honorType'    => 'nullable|string|in:memory,honor',
            'honorName'    => 'nullable|string|max:255|required_with:honorType',
            'address1'     => 'required|string',
            'city'         => 'required|string',
            'state'        => 'required|string',
            'country'      => 'required|string',
        ]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->capturePaymentOrder($request->orderID);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                $goal = FundRaisa::latest()->first();

                $donation = GeneralDonation::create([
                    'fund_raisa_id'   => $goal?->id,
                    'donation_for'    => $request->donation_for,
                    'other_purpose'   => $request->donation_for === 'Other' ? $request->input('otherPurpose') : null,
                    'honor_type'      => $request->input('honorType'),
                    'honor_name'      => $request->input('honorName'),
                    'name'            => $request->name,
                    'email'           => $request->email,
                    'amount'          => $request->amount,
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

                $this->sendDonationEmails($donation, $this->buildSafePayload($request, $donation));

                return response()->json(['status' => 'success', 'donation' => $donation]);
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
            'amount'       => 'required|numeric|min:1',
            'interval'     => 'required|string|in:month,year',
            'donation_for' => ['required', 'string', Rule::in(self::DONATION_PURPOSES)],
            'otherPurpose' => 'nullable|string|max:255|required_if:donation_for,Other',
            'honorType'    => 'nullable|string|in:memory,honor',
            'honorName'    => 'nullable|string|max:255|required_with:honorType',
            'name'         => 'required|string',
            'email'        => 'required|email',
            'return_url'   => 'nullable|url',
            'cancel_url'   => 'nullable|url',
            // Include address validation if you want to save it before the redirect
        ]);

        try {
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
            $planName = "Donation: " . $request->donation_for . " ($" . $request->amount . ")";
            $planDetails = [
                "product_id" => $productId,
                "name" => $planName,
                "description" => $planName,
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
                                "value" => number_format($request->amount, 2, '.', ''),
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

            return response()->json($subscription);
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
            'amount'         => 'required',
            'email'          => 'required|email',
            'donation_for'   => ['required', 'string', Rule::in(self::DONATION_PURPOSES)],
            'otherPurpose'   => 'nullable|string|max:255|required_if:donation_for,Other',
            'honorType'      => 'nullable|string|in:memory,honor',
            'honorName'      => 'nullable|string|max:255|required_with:honorType',
            'interval'       => 'required',
        ]);

        $goal = FundRaisa::latest()->first();

        $donation = GeneralDonation::create([
            'fund_raisa_id'   => $goal?->id,
            'donation_for'    => $request->donation_for,
            'other_purpose'   => $request->donation_for === 'Other' ? $request->input('otherPurpose') : null,
            'honor_type'      => $request->input('honorType'),
            'honor_name'      => $request->input('honorName'),
            'name'            => $request->name,
            'email'           => $request->email,
            'amount'          => $request->amount,
            'payment_id'      => $request->subscriptionID,
            'donation_mode'   => 'paypal',
            'frequency'       => $request->interval, // 'month' or 'year'
            'status'          => 'paid', // Or 'active'
            'address1'        => $request->address1,
            'city'            => $request->city,
            'state'           => $request->state,
            'country'         => $request->country,
        ]);

        $this->sendDonationEmails($donation, $this->buildSafePayload($request, $donation));

        return response()->json(['status' => 'success', 'donation' => $donation]);
    }
}
