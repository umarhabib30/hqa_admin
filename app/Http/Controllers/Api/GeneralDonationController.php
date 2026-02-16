<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FundRaisa;
use App\Models\GeneralDonation;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;
use Exception;

class GeneralDonationController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
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
            // For legacy flows, allow passing a payment_method id (pm_*) and we will use it.
            'payment_method' => 'nullable|string',
            'email'          => 'required|email',
            'name'           => 'nullable|string',
            'amount'         => 'required|integer|min:1',
            'interval'       => 'required|string|in:month,year',
            'donation_for'   => 'required|string|max:255',
            'address1'       => 'required|string|max:255',
            'address2'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'state'          => 'required|string|max:255',
            'country'        => 'required|string|max:255',
        ]);

        try {
            // 1. Create or Get Customer
            $customerPayload = [
                'email' => $request->email,
                'name'  => $request->name,
                'metadata' => ['purpose' => $request->donation_for],
            ];
            if ($request->filled('payment_method')) {
                $customerPayload['payment_method'] = $request->payment_method;
                $customerPayload['invoice_settings'] = ['default_payment_method' => $request->payment_method];
            }
            $customer = $this->stripe->customers->create($customerPayload);

            // 2. Create Product & Price dynamically
            $product = $this->stripe->products->create(['name' => 'Donation: ' . $request->donation_for]);
            $price   = $this->stripe->prices->create([
                'unit_amount' => (int) $request->amount * 100,
                'currency'    => 'usd',
                'recurring'   => ['interval' => $request->interval],
                'product'     => $product->id,
            ]);

            // 3. Create Subscription (default_incomplete so frontend can confirm via PaymentElement if needed)
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $customer->id,
                'items'    => [['price' => $price->id]],
                'collection_method' => 'charge_automatically',
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'payment_method_types' => ['card'],
                    'save_default_payment_method' => 'on_subscription',
                ],
                'metadata' => ['purpose' => $request->donation_for],
                'expand' => ['latest_invoice', 'latest_invoice.payment_intent'],
            ]);

            $goal = FundRaisa::latest()->first();

            // Extract PaymentIntent client_secret for the first invoice
            $pi = null;
            $clientSecret = null;
            $latestInvoice = $subscription->latest_invoice ?? null;

            try {
                // If latest_invoice is missing, re-fetch subscription with expand
                if (empty($latestInvoice) && !empty($subscription->id)) {
                    $subscription = $this->stripe->subscriptions->retrieve($subscription->id, [
                        'expand' => ['latest_invoice', 'latest_invoice.payment_intent'],
                    ]);
                    $latestInvoice = $subscription->latest_invoice ?? null;
                }

                // If latest_invoice isn't expanded, retrieve it
                if (is_string($latestInvoice) && $latestInvoice !== '') {
                    $latestInvoice = $this->stripe->invoices->retrieve($latestInvoice, [
                        'expand' => ['payment_intent'],
                    ]);
                }

                // Fallback: if we still don't have an invoice object, list invoices by subscription
                if (!is_object($latestInvoice) && !empty($subscription->id)) {
                    $invoices = $this->stripe->invoices->all([
                        'subscription' => $subscription->id,
                        'limit' => 1,
                        'expand' => ['data.payment_intent'],
                    ]);
                    $latestInvoice = $invoices->data[0] ?? null;
                }

                if (is_object($latestInvoice)) {
                    // If invoice is draft, finalize so a PaymentIntent gets created
                    if (($latestInvoice->status ?? null) === 'draft' && !empty($latestInvoice->id)) {
                        $this->stripe->invoices->finalizeInvoice($latestInvoice->id);
                        $latestInvoice = $this->stripe->invoices->retrieve($latestInvoice->id, [
                            'expand' => ['payment_intent'],
                        ]);
                    }

                    $pi = $latestInvoice->payment_intent ?? null;

                    // If payment_intent isn't expanded, retrieve it
                    if (is_string($pi) && $pi !== '') {
                        $pi = $this->stripe->paymentIntents->retrieve($pi);
                    }

                    if (is_object($pi)) {
                        $clientSecret = $pi->client_secret ?? null;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore extraction failures; we'll return a safe response below.
                $clientSecret = null;
            }

            $donation = GeneralDonation::create([
                'fund_raisa_id'          => $goal?->id,
                'donation_for'           => $request->donation_for,
                'name'                   => $request->name,
                'email'                  => $request->email,
                'amount'                 => (int) $request->amount,
                'payment_id'             => $subscription->id,
                'donation_mode'          => 'stripe',
                'frequency'              => $request->interval,
                'stripe_customer_id'     => $customer->id,
                'stripe_subscription_id' => $subscription->id,
                'status'                 => ($subscription->status === 'active') ? 'paid' : 'pending',
                'address1'               => $request->address1,
                'address2'               => $request->address2,
                'city'                   => $request->city,
                'state'                  => $request->state,
                'country'                => $request->country,
            ]);

            // If Stripe created a PaymentIntent for the first invoice, frontend must confirm it (3DS/wallets)
            if (!empty($clientSecret)) {
                return response()->json([
                    'paid' => false,
                    'donation_id' => $donation->id,
                    'subscription_id' => $subscription->id,
                    'client_secret' => $clientSecret,
                    'pi_status' => is_object($pi) ? ($pi->status ?? null) : null,
                    'subscription_status' => $subscription->status ?? null,
                ], 200);
            }

            // If subscription isn't active and we still have no client_secret, avoid frontend IntegrationError
            if (($subscription->status ?? null) !== 'active') {
                Log::warning('Stripe recurring missing client_secret', [
                    'subscription_id' => $subscription->id ?? null,
                    'subscription_status' => $subscription->status ?? null,
                    'latest_invoice' => is_object($latestInvoice) ? ($latestInvoice->id ?? null) : $latestInvoice,
                    'invoice_status' => is_object($latestInvoice) ? ($latestInvoice->status ?? null) : null,
                    'payment_intent' => is_object($pi) ? ($pi->id ?? null) : $pi,
                ]);
                return response()->json([
                    'paid' => false,
                    'donation_id' => $donation->id,
                    'subscription_id' => $subscription->id,
                    'subscription_status' => $subscription->status ?? null,
                    'invoice_id' => is_object($latestInvoice) ? ($latestInvoice->id ?? null) : null,
                    'message' => 'Subscription created but payment client_secret is missing. Check Stripe subscription latest_invoice/payment_intent.',
                ], 200);
            }

            return response()->json([
                'paid' => ($subscription->status === 'active'),
                'donation_id' => $donation->id,
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status ?? null,
            ], 200);
        } catch (Exception $e) {
            Log::error('Stripe Recurring Error: ' . $e->getMessage());
            return response()->json(['paid' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * One-time donation (Stripe)
     */
    public function oneTimeDonation(Request $request)
    {
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
            'donation_for'   => 'required|string',
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
        $request->validate([
            'orderID'      => 'required',
            'email'        => 'required|email',
            'amount'       => 'required|numeric',
            'donation_for' => 'required|string',
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
        $request->validate([
            'amount'       => 'required|numeric|min:1',
            'interval'     => 'required|string|in:month,year',
            'donation_for' => 'required|string',
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
        $request->validate([
            'subscriptionID' => 'required',
            'amount'         => 'required',
            'email'          => 'required|email',
            'donation_for'   => 'required',
            'interval'       => 'required',
        ]);

        $goal = FundRaisa::latest()->first();

        $donation = GeneralDonation::create([
            'fund_raisa_id'   => $goal?->id,
            'donation_for'    => $request->donation_for,
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

        return response()->json(['status' => 'success', 'donation' => $donation]);
    }
}
