<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Subscription;
use Stripe\SetupIntent;

use App\Models\GeneralDonation;

class GeneralDonationController extends Controller
{
    /**
     * Step 1: Initialize a donation (one-time or recurring)
     */
    public function processDonation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email',
            'amount'    => 'required|numeric|min:0.50',
            'frequency' => 'required|in:once,month,year',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $amountInCents = (int) round($validated['amount'] * 100);

            // 1️⃣ Handle Stripe Customer
            $customers = Customer::all(['email' => $validated['email'], 'limit' => 1]);
            $customer = count($customers->data) > 0
                ? $customers->data[0]
                : Customer::create([
                    'email' => $validated['email'],
                    'name'  => $validated['name'],
                ]);

            // 2️⃣ ONE-TIME DONATION
            if ($validated['frequency'] === 'once') {
                $paymentIntent = PaymentIntent::create([
                    'amount' => $amountInCents,
                    'currency' => 'usd',
                    'customer' => $customer->id,
                    'payment_method_types' => ['card'],
                    'metadata' => ['donor_name' => $validated['name']],
                ]);

                return response()->json([
                    'success' => true,
                    'paymentType' => 'once',
                    'clientSecret' => $paymentIntent->client_secret,
                    'customerId' => $customer->id,
                ]);
            }

            // 3️⃣ RECURRING DONATION (month/year)
            $setupIntent = SetupIntent::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
            ]);

            $price = Price::create([
                'unit_amount' => $amountInCents,
                'currency' => 'usd',
                'recurring' => ['interval' => $validated['frequency']], // month/year
                'product_data' => [
                    'name' => 'General Donation (' . ucfirst($validated['frequency']) . ')',
                ],
            ]);

            return response()->json([
                'success' => true,
                'paymentType' => 'recurring_setup',
                'clientSecret' => $setupIntent->client_secret,
                'customerId' => $customer->id,
                'priceId' => $price->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe Init Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stripe Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 2: Create subscription after SetupIntent succeeds
     */
    public function createSubscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customerId' => 'required|string',
            'priceId' => 'required|string',
            'paymentMethodId' => 'required|string',
            'subscriptionId' => 'nullable|string', // optional: reuse existing subscription
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Attach payment method to customer
            $pm = \Stripe\PaymentMethod::retrieve($validated['paymentMethodId']);
            $pm->attach(['customer' => $validated['customerId']]);

            Customer::update($validated['customerId'], [
                'invoice_settings' => ['default_payment_method' => $pm->id],
            ]);

            $subscription = null;
            $paymentIntent = null;

            if (!empty($validated['subscriptionId'])) {
                // 🔹 Reuse existing subscription
                $subscription = Subscription::retrieve($validated['subscriptionId'], ['expand' => ['latest_invoice.payment_intent']]);
                $paymentIntent = $subscription->latest_invoice->payment_intent ?? null;
            }

            if (!$subscription) {
                // 🔹 Create new subscription
                $subscription = Subscription::create([
                    'customer' => $validated['customerId'],
                    'items' => [['price' => $validated['priceId']]],
                    'payment_behavior' => 'default_incomplete',
                    'expand' => ['latest_invoice.payment_intent'],
                ]);

                $paymentIntent = $subscription->latest_invoice->payment_intent ?? null;
            }

            return response()->json([
                'success' => true,
                'subscriptionId' => $subscription->id,
                'clientSecret' => $paymentIntent ? $paymentIntent->client_secret : null,
                'message' => $paymentIntent ? 'Ready to pay' : 'Subscription created, waiting for first invoice',
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe Subscription Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Step 3: Confirm one-time donation after payment succeeds
     */
    public function confirmDonation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'amount' => 'required|numeric',
            'frequency' => 'required|in:once,month,year',
            'payment_id' => 'nullable|string',
            'stripe_customer_id' => 'nullable|string',
            'stripe_subscription_id' => 'nullable|string',
        ]);

        try {
            $donationData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'amount' => $validated['amount'],
                'frequency' => $validated['frequency'],
                'stripe_customer_id' => $validated['stripe_customer_id'] ?? null,
                'stripe_subscription_id' => $validated['stripe_subscription_id'] ?? null,
                'status' => 'active',
            ];

            // Only save payment_id for one-time donations
            if ($validated['frequency'] === 'once' && !empty($validated['payment_id'])) {
                $donationData['payment_id'] = $validated['payment_id'];
            }

            $donation = GeneralDonation::create($donationData);

            return response()->json(['success' => true, 'data' => $donation]);
        } catch (\Throwable $e) {
            Log::error('Database Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment succeeded, but database record failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
