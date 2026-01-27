<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralDonation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\Subscription;

class GeneralDonationController extends Controller
{
    public function processDonation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0.50',
            'frequency' => 'required|in:once,month,year',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $amountInCents = (int) round($validated['amount'] * 100);

            Log::info('Donation initiated', [
                'email' => $validated['email'],
                'amount' => $validated['amount'],
                'frequency' => $validated['frequency'],
            ]);

            // 1) Handle Customer
            $customers = Customer::all(['email' => $validated['email'], 'limit' => 1]);
            $customer = count($customers->data) > 0
                ? $customers->data[0]
                : Customer::create([
                    'email' => $validated['email'],
                    'name' => $validated['name'],
                ]);

            Log::info('Stripe customer resolved', [
                'customer_id' => $customer->id,
            ]);

            // 2) One-Time Payment
            if ($validated['frequency'] === 'once') {
                $pi = PaymentIntent::create([
                    'amount' => $amountInCents,
                    'currency' => 'usd',
                    'customer' => $customer->id,
                    'payment_method_types' => ['card'],
                ]);

                Log::info('One-time PaymentIntent created', [
                    'payment_intent' => $pi->id,
                ]);

                return response()->json([
                    'success' => true,
                    'paymentType' => 'once',
                    'clientSecret' => $pi->client_secret,
                    'customerId' => $customer->id,
                ]);
            }

            // 3) Recurring Subscription Flow
            $price = Price::create([
                'unit_amount' => $amountInCents,
                'currency' => 'usd',
                'recurring' => ['interval' => $validated['frequency']],
                'product_data' => [
                    'name' => 'General Donation (' . ucfirst($validated['frequency']) . ')',
                ],
            ]);

            Log::info('Stripe price created', [
                'price_id' => $price->id,
            ]);

            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            Log::info('Stripe subscription created', [
                'subscription_id' => $subscription->id,
            ]);

            $clientSecret = $subscription->latest_invoice->payment_intent->client_secret ?? null;

            if (!$clientSecret) {
                Log::warning('Subscription created without PaymentIntent', [
                    'subscription_id' => $subscription->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'paymentType' => 'recurring_first_payment',
                'customerId' => $customer->id,
                'priceId' => $price->id,
                'subscriptionId' => $subscription->id,
                'clientSecret' => $clientSecret,
            ]);

        } catch (\Throwable $e) {
            Log::error('processDonation failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['card', 'payment_method']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed',
            ], 500);
        }
    }

    public function createSubscription(Request $request): JsonResponse
    {
        try {
            throw new \Exception(
                'createSubscription endpoint is deprecated and should not be used.'
            );
        } catch (\Throwable $e) {
            Log::error('createSubscription called unexpectedly', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint',
            ], 400);
        }
    }

    public function confirmDonation(Request $request): JsonResponse
    {
        try {
            $donation = GeneralDonation::create($request->all());

            Log::info('Donation stored in DB', [
                'donation_id' => $donation->id,
                'payment_id' => $request->payment_id ?? null,
                'subscription_id' => $request->stripe_subscription_id ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $donation,
            ]);
        } catch (\Throwable $e) {
            Log::error('confirmDonation failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store donation',
            ], 500);
        }
    }
}
