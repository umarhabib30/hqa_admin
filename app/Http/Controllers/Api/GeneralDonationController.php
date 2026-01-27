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
            $customers = Customer::all([
                'email' => $validated['email'],
                'limit' => 1
            ]);

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
                    'payment_intent_id' => $pi->id,
                ]);

                return response()->json([
                    'success' => true,
                    'paymentType' => 'once',
                    'clientSecret' => $pi->client_secret,
                    'customerId' => $customer->id,
                ]);
            }

            // 3) Recurring Subscription Flow (FIXED)
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

                // ensure Stripe tries to charge automatically
                'collection_method' => 'charge_automatically',

                // create invoice + PI requiring confirmation
                'payment_behavior' => 'default_incomplete',

                // force card + save it for future renewals
                'payment_settings' => [
                    'payment_method_types' => ['card'],
                    'save_default_payment_method' => 'on_subscription',
                ],

                'expand' => ['latest_invoice.payment_intent'],
            ]);

            Log::info('Stripe subscription created', [
                'subscription_id' => $subscription->id,
            ]);

            $clientSecret = $subscription->latest_invoice->payment_intent->client_secret ?? null;

            // Fallback: finalize invoice if PI is missing
            if (!$clientSecret) {
                $invoiceId = is_string($subscription->latest_invoice)
                    ? $subscription->latest_invoice
                    : ($subscription->latest_invoice->id ?? null);

                Log::warning('Subscription created without PaymentIntent (attempting finalize)', [
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoiceId,
                ]);

                if ($invoiceId) {
                    $invoice = Invoice::retrieve([
                        'id' => $invoiceId,
                        'expand' => ['payment_intent'],
                    ]);

                    Log::info('Invoice retrieved', [
                        'invoice_id' => $invoiceId,
                        'invoice_status' => $invoice->status ?? null,
                        'collection_method' => $invoice->collection_method ?? null,
                    ]);

                    // If draft, finalize it (usually creates PI)
                    if (($invoice->status ?? null) === 'draft') {
                        $invoice = Invoice::finalizeInvoice($invoiceId, [
                            'expand' => ['payment_intent'],
                        ]);

                        Log::info('Invoice finalized', [
                            'invoice_id' => $invoiceId,
                            'invoice_status' => $invoice->status ?? null,
                        ]);
                    }

                    $clientSecret = $invoice->payment_intent->client_secret ?? null;

                    if (!$clientSecret) {
                        Log::error('Invoice still has no PaymentIntent after finalize', [
                            'subscription_id' => $subscription->id,
                            'invoice_id' => $invoiceId,
                            'invoice_status' => $invoice->status ?? null,
                            'collection_method' => $invoice->collection_method ?? null,
                        ]);
                    }
                } else {
                    Log::error('No invoiceId found on subscription.latest_invoice', [
                        'subscription_id' => $subscription->id,
                    ]);
                }
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
        // This endpoint is no longer needed for the updated recurring flow.
        // Keep it for backward compatibility but log if it is called.
        try {
            Log::warning('Deprecated createSubscription endpoint called', [
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'This endpoint is deprecated. Use processDonation recurring flow.',
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
