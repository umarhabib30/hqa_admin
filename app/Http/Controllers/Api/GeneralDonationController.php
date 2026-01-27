<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralDonation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Invoice;
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

            $amountInCents = (int) round(((float)$validated['amount']) * 100);

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
                    'name'  => $validated['name'],
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
                    'payment_intent_id' => $pi->id,
                ]);

                return response()->json([
                    'success' => true,
                    'paymentType' => 'once',
                    'clientSecret' => $pi->client_secret,
                    'customerId' => $customer->id,
                ]);
            }

            // 3) Recurring Flow
            $price = Price::create([
                'unit_amount' => $amountInCents,
                'currency' => 'usd',
                'recurring' => ['interval' => $validated['frequency']], // month|year
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
                'collection_method' => 'charge_automatically',
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'payment_method_types' => ['card'],
                    'save_default_payment_method' => 'on_subscription',
                ],
                // expand latest_invoice so we can get invoice id reliably
                'expand' => ['latest_invoice'],
            ]);

            Log::info('Stripe subscription created', [
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status ?? null,
            ]);

            // Get invoice id
            $invoiceId = null;
            if (is_string($subscription->latest_invoice)) {
                $invoiceId = $subscription->latest_invoice;
            } elseif (isset($subscription->latest_invoice->id)) {
                $invoiceId = $subscription->latest_invoice->id;
            }

            if (!$invoiceId) {
                Log::error('No invoiceId found on subscription.latest_invoice', [
                    'subscription_id' => $subscription->id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to initialize recurring invoice',
                ], 500);
            }

            // Retrieve invoice and read client secret from either:
            // - invoice.payment_intent.client_secret (older)
            // - invoice.confirmation_secret.client_secret (newer API behavior)
            $invoice = Invoice::retrieve($invoiceId);

            Log::info('Invoice retrieved', [
                'invoice_id' => $invoiceId,
                'invoice_status' => $invoice->status ?? null,
                'collection_method' => $invoice->collection_method ?? null,
                'amount_due' => $invoice->amount_due ?? null,
            ]);

            $clientSecret = null;

            // Older behavior: payment_intent exists
            if (isset($invoice->payment_intent) && is_object($invoice->payment_intent)) {
                $clientSecret = $invoice->payment_intent->client_secret ?? null;
            }

            // Newer behavior: confirmation_secret exists (preferred fallback)
            if (!$clientSecret && isset($invoice->confirmation_secret) && is_object($invoice->confirmation_secret)) {
                $clientSecret = $invoice->confirmation_secret->client_secret ?? null;
            }

            if (!$clientSecret) {
                Log::error('No client secret found on invoice (payment_intent and confirmation_secret both missing)', [
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoiceId,
                    'invoice_status' => $invoice->status ?? null,
                    'collection_method' => $invoice->collection_method ?? null,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to initialize first recurring payment',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'paymentType' => 'recurring_first_payment',
                'customerId' => $customer->id,
                'priceId' => $price->id,
                'subscriptionId' => $subscription->id,
                'invoiceId' => $invoiceId,
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
        // Not needed for the new flow; keep for backward compatibility.
        try {
            Log::warning('Deprecated createSubscription endpoint called', [
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Deprecated endpoint. Use processDonation recurring flow.',
            ], 400);

        } catch (\Throwable $e) {
            Log::error('createSubscription failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
