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
        $request->validate([
            'payment_method' => 'required|string',
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
            $customer = $this->stripe->customers->create([
                'email' => $request->email,
                'name'  => $request->name,
                'payment_method' => $request->payment_method,
                'invoice_settings' => ['default_payment_method' => $request->payment_method],
                'metadata' => ['purpose' => $request->donation_for],
            ]);

            // 2. Create Product & Price dynamically
            $product = $this->stripe->products->create(['name' => 'Donation: ' . $request->donation_for]);
            $price   = $this->stripe->prices->create([
                'unit_amount' => (int) $request->amount * 100,
                'currency'    => 'usd',
                'recurring'   => ['interval' => $request->interval],
                'product'     => $product->id,
            ]);

            // 3. Create Subscription
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $customer->id,
                'items'    => [['price' => $price->id]],
                'off_session' => true,
                'confirm' => true,
                'payment_behavior' => 'allow_incomplete',
                'metadata' => ['purpose' => $request->donation_for],
            ]);

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
                'status'                 => ($subscription->status === 'active') ? 'paid' : 'pending',
                'address1'               => $request->address1,
                'address2'               => $request->address2,
                'city'                   => $request->city,
                'state'                  => $request->state,
                'country'                => $request->country,
            ]);

            return response()->json(['paid' => true, 'donation_id' => $donation->id], 200);
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
        $request->validate([
            'payment_method' => 'required|string',
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
                'payment_method' => $request->payment_method,
            ]);

            $pi = $this->stripe->paymentIntents->create([
                'amount'   => (int) $request->amount * 100,
                'currency' => 'usd',
                'customer' => $customer->id,
                'payment_method' => $request->payment_method,
                'confirm'  => true,
                'off_session' => true,
                'description'   => 'One-time Donation: ' . $request->donation_for,
                'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
            ]);

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

            return response()->json(['paid' => $pi->status === 'succeeded', 'donation_id' => $donation->id]);
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
            // Include address validation if you want to save it before the redirect
        ]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // 1. DYNAMIC PRODUCT CREATION
            $productData = [
                "name" => "Donation Service",
                "description" => "Recurring donation for " . config('app.name'),
                "type" => "SERVICE",
                "category" => "CHARITY_AND_NON_PROFIT_ORGANIZATIONS"
            ];

            // Avoid creating duplicate products by using a consistent Request-ID
            $request_id = 'PRODUCT-' . md5('donation-service-fixed');
            $product = $provider->createProduct($productData, $request_id);
            $productId = $product['id'] ?? 'DONATION-SERVICE';

            // 2. CREATE DYNAMIC PLAN
            $planName = "Donation: " . $request->donation_for . " ($" . $request->amount . ")";
            $planDetails = [
                "product_id" => $productId,
                "name" => $planName,
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

            // 3. CREATE SUBSCRIPTION
            $subscriptionData = [
                "plan_id" => $plan['id'],
                "application_context" => [
                    "brand_name" => config('app.name'),
                    "shipping_preference" => "NO_SHIPPING",
                    "user_action" => "SUBSCRIBE_NOW",
                ]
            ];

            $subscription = $provider->createSubscription($subscriptionData);

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
