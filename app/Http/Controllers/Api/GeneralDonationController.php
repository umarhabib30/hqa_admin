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
            'donation_for'   => 'required|string|max:255',
            'address1'       => 'required|string|max:255',
            'address2'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'state'          => 'required|string|max:255',
            'country'        => 'required|string|max:255',
            // optional: customer_id if you want to reuse the same customer in call #2
            'customer_id'    => 'nullable|string',
        ]);
    
        try {
            $paymentMethodId = $request->filled('payment_method') ? $request->payment_method : null;
    
            // ✅ Reuse customer if provided, otherwise create new
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
             * ✅ IMPORTANT FIX:
             * If no pm_... provided, return a SetupIntent client_secret.
             * Frontend will confirmSetup() and then call this endpoint again with pm_...
             */
            if (empty($paymentMethodId)) {
                $si = $this->stripe->setupIntents->create([
                    'customer' => $customer->id,
                    'usage' => 'off_session',
                    'payment_method_types' => ['card'],
                    'metadata' => ['purpose' => $request->donation_for],
                ]);
    
                return response()->json([
                    'paid' => false,
                    'client_secret' => $si->client_secret,   // ✅ seti_...
                    'intent_type' => 'setup_intent',
                    'customer_id' => $customer->id,
                    'payment_method_provided' => false,
                ], 200);
            }
    
            // ✅ Attach PM + set as default for invoices
            $this->stripe->paymentMethods->attach($paymentMethodId, [
                'customer' => $customer->id,
            ]);
    
            $this->stripe->customers->update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);
    
            // 2) Create Product & Price
            $product = $this->stripe->products->create([
                'name' => 'Donation: ' . $request->donation_for
            ]);
    
            $price = $this->stripe->prices->create([
                'unit_amount' => (int) $request->amount * 100,
                'currency'    => 'usd',
                'recurring'   => ['interval' => $request->interval],
                'product'     => $product->id,
            ]);
    
            // 3) Create Subscription and expand PI
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $customer->id,
                'items'    => [['price' => $price->id]],
                'collection_method' => 'charge_automatically',
                'payment_behavior' => 'default_incomplete',
                'default_payment_method' => $paymentMethodId,
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                ],
                'expand' => ['latest_invoice.payment_intent', 'pending_setup_intent'],
                'metadata' => ['purpose' => $request->donation_for],
            ]);

            // --- Ensure we are dealing with the FIRST (current) invoice, not the upcoming preview ---
            $invoiceRef = $subscription->latest_invoice ?? null;
            $invoiceId = is_string($invoiceRef) ? $invoiceRef : ($invoiceRef->id ?? null);

            $invoice = null;
            if (!empty($invoiceId)) {
                $invoice = $this->stripe->invoices->retrieve($invoiceId, [
                    'expand' => ['payment_intent'],
                ]);

                // If it's still a draft, finalize it so Stripe can create the PaymentIntent.
                if (($invoice->status ?? null) === 'draft') {
                    $invoice = $this->stripe->invoices->finalizeInvoice($invoiceId, [
                        'expand' => ['payment_intent'],
                    ]);
                }

                // Re-fetch once more (helps when PI is created asynchronously at finalization)
                $invoice = $this->stripe->invoices->retrieve($invoiceId, [
                    'expand' => ['payment_intent'],
                ]);
            }

            $pi = $invoice?->payment_intent ?? null;
            if (is_string($pi)) {
                $pi = $this->stripe->paymentIntents->retrieve($pi);
            }

            $setupIntent = $subscription->pending_setup_intent ?? null;
            if (is_string($setupIntent)) {
                $setupIntent = $this->stripe->setupIntents->retrieve($setupIntent);
            }

            // Decide what the frontend should confirm *immediately*.
            $intentType = null;
            $clientSecret = null;
            if ($pi && !empty($pi->client_secret)) {
                $intentType = 'payment_intent';
                $clientSecret = $pi->client_secret;
            } elseif ($setupIntent && !empty($setupIntent->client_secret)) {
                $intentType = 'setup_intent';
                $clientSecret = $setupIntent->client_secret;
            }

            $paid =
                ($subscription->status === 'active' || $subscription->status === 'trialing') ||
                (!empty($invoice) && !empty($invoice->paid)) ||
                (!empty($pi) && ($pi->status ?? null) === 'succeeded');

            // Save donation record (recurring)
            $goal = FundRaisa::latest()->first();
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
                'status'                 => $paid ? 'paid' : 'pending',
                'address1'               => $request->address1,
                'address2'               => $request->address2,
                'city'                   => $request->city,
                'state'                  => $request->state,
                'country'                => $request->country,
            ]);

            return response()->json([
                'paid' => $paid,
                'donation_id' => $donation->id,
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status,
                'invoice_id' => $invoiceId,
                'invoice_status' => $invoice?->status,
                'intent_type' => $intentType,                 // payment_intent | setup_intent | null
                'client_secret' => $clientSecret,             // pi_... or seti_...
                'pi_status' => $pi?->status,
                'si_status' => $setupIntent?->status,
                'customer_id' => $customer->id,
                'payment_method_provided' => true,
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