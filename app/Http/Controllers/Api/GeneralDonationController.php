<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\SetupIntent;
use App\Models\GeneralDonation;

class GeneralDonationController extends Controller
{
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

            // 1. Handle Customer
            $customers = Customer::all(['email' => $validated['email'], 'limit' => 1]);
            $customer = count($customers->data) > 0
                ? $customers->data[0]
                : Customer::create(['email' => $validated['email'], 'name' => $validated['name']]);

            // 2. One-Time Flow
            if ($validated['frequency'] === 'once') {
                $pi = PaymentIntent::create([
                    'amount' => $amountInCents,
                    'currency' => 'usd',
                    'customer' => $customer->id,
                    'payment_method_types' => ['card'],
                ]);

                return response()->json([
                    'success' => true,
                    'paymentType' => 'once',
                    'clientSecret' => $pi->client_secret,
                    'customerId' => $customer->id,
                ]);
            }

            // 3. Recurring Setup Flow
            $si = SetupIntent::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
            ]);

            $price = Price::create([
                'unit_amount' => $amountInCents,
                'currency' => 'usd',
                'recurring' => ['interval' => $validated['frequency']],
                'product_data' => ['name' => 'General Donation (' . ucfirst($validated['frequency']) . ')'],
            ]);

            return response()->json([
                'success' => true,
                'paymentType' => 'recurring_setup',
                'clientSecret' => $si->client_secret,
                'customerId' => $customer->id,
                'priceId' => $price->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function createSubscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customerId' => 'required|string',
            'priceId' => 'required|string',
            'paymentMethodId' => 'required|string',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Set default payment method
            $pm = \Stripe\PaymentMethod::retrieve($validated['paymentMethodId']);
            $pm->attach(['customer' => $validated['customerId']]);
            Customer::update($validated['customerId'], [
                'invoice_settings' => ['default_payment_method' => $pm->id],
            ]);

            // Create subscription and expand the latest invoice
            $subscription = Subscription::create([
                'customer' => $validated['customerId'],
                'items' => [['price' => $validated['priceId']]],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // --- THE FIX FOR THE "NULL" ERROR ---
            $clientSecret = null;
            if (isset($subscription->latest_invoice->payment_intent)) {
                $clientSecret = $subscription->latest_invoice->payment_intent->client_secret;
            }

            return response()->json([
                'success' => true,
                'subscriptionId' => $subscription->id,
                'clientSecret' => $clientSecret, 
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function confirmDonation(Request $request): JsonResponse
    {
        try {
            $donation = GeneralDonation::create($request->all());
            return response()->json(['success' => true, 'data' => $donation]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
