<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FundRaisa;
use App\Models\GeneralDonation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Subscription;

class GeneralDonationController extends Controller
{
    public function show()
    {
        return view('subscribe_dynamic');
    }

    // recurring donation
    public function recurringDonation(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'email' => 'required|email',
            'name' => 'nullable|string',
            'amount' => 'required|integer|min:50',
            'interval' => 'required|string|in:month,year',
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));
        // Customer
        $customer = $stripe->customers->create([
            'email' => $request->email,
            'name' => $request->name,
        ]);

        // Attach PM
        $stripe->paymentMethods->attach($request->payment_method, [
            'customer' => $customer->id,
        ]);

        // Default PM
        $stripe->customers->update($customer->id, [
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
        ]);

        // Product
        $product = $stripe->products->create([
            'name' => 'HQA Funding',
        ]);

        // Price
        $price = $stripe->prices->create([
            'unit_amount' => (int) $request->amount,
            'currency' => 'usd',
            'recurring' => ['interval' => $request->interval],
            'product' => $product->id,
        ]);

        // Subscription
        $subscription = $stripe->subscriptions->create([
            'customer' => $customer->id,
            'items' => [
                ['price' => $price->id],
            ],
            'collection_method' => 'charge_automatically',
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => [
                'save_default_payment_method' => 'on_subscription',
                'payment_method_types' => ['card'],
            ],
            'expand' => ['latest_invoice'],
        ]);

        $invoiceId = is_string($subscription->latest_invoice)
            ? $subscription->latest_invoice
            : ($subscription->latest_invoice->id ?? null);

        if (!$invoiceId) {
            return response()->json([
                'message' => 'No latest_invoice found on subscription.',
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status,
            ], 200);
        }

        // Helper: always re-fetch invoice with expansions
        $getInvoice = function () use ($stripe, $invoiceId) {
            return $stripe->invoices->retrieve($invoiceId, [
                'expand' => ['payment_intent', 'charge'],
            ]);
        };

        $invoice = $getInvoice();

        // If draft, finalize
        if (($invoice->status ?? null) === 'draft') {
            $stripe->invoices->finalizeInvoice($invoiceId, []);
            $invoice = $getInvoice();
        }

        // If open and still unpaid, pay now
        if (($invoice->status ?? null) === 'open') {
            // Try to pay using the PM we just attached
            $stripe->invoices->pay($invoiceId, [
                'payment_method' => $request->payment_method,
            ]);
            $invoice = $getInvoice();
        }

        // Re-fetch subscription to get updated status (important)
        $subscriptionFresh = $stripe->subscriptions->retrieve($subscription->id, []);

        // ✅ Case A: Invoice is PAID (no client_secret needed)
        if (($invoice->status ?? null) === 'paid') {
            $goal = FundRaisa::latest()->first();

            // Store donation (initial)
            $donation = GeneralDonation::create([
                'fund_raisa_id' => $goal->id,  // add to validation if required
                'name' => $request->name,
                'email' => $request->email,
                'amount' => (int) $request->amount,
                'payment_id' => $invoiceId,  // or $subscription->id, see note below
                'donation_mode' => 'stripe',
                'frequency' => $request->interval,  // month/year
                'stripe_customer_id' => $customer->id,
                'stripe_subscription_id' => $subscription->id,
                'status' => 'paid',
            ]);

            return response()->json([
                'paid' => true,
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'price_id' => $price->id,
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscriptionFresh->status ?? $subscription->status,
                'latest_invoice_id' => $invoiceId,
                'invoice_status' => $invoice->status ?? null,
                'amount_due' => $invoice->amount_due ?? null,
                'total' => $invoice->total ?? null,
                // Sometimes charge exists even if payment_intent isn't present in invoice
                'charge_id' => $invoice->charge->id ?? $invoice->charge ?? null,
                'message' => 'Invoice is already paid. No client_secret required.',
            ], 200);
        }

        // ✅ Case B: We have a PaymentIntent → return client_secret
        // payment_intent can be an object (expanded) or an ID string (depending)
        $pi = $invoice->payment_intent ?? null;

        if (is_string($pi)) {
            $pi = $stripe->paymentIntents->retrieve($pi, []);
        }

        if ($pi && isset($pi->client_secret)) {
            return response()->json([
                'paid' => false,
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'price_id' => $price->id,
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscriptionFresh->status ?? $subscription->status,
                'latest_invoice_id' => $invoiceId,
                'invoice_status' => $invoice->status ?? null,
                'client_secret' => $pi->client_secret,
                'pi_status' => $pi->status,
            ], 200);
        }

        // ✅ Case C: Still no PI and not paid → return debug
        return response()->json([
            'paid' => false,
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'price_id' => $price->id,
            'subscription_id' => $subscription->id,
            'subscription_status' => $subscriptionFresh->status ?? $subscription->status,
            'latest_invoice_id' => $invoiceId,
            'invoice_status' => $invoice->status ?? null,
            'amount_due' => $invoice->amount_due ?? null,
            'total' => $invoice->total ?? null,
            'message' => 'Invoice not paid and no payment_intent found. Check invoice in Stripe dashboard.',
        ], 200);
    }

    // one time donation
    public function oneTimeDonation(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|string',
            'email'          => 'required|email',
            'name'           => 'nullable|string',
            'amount'         => 'required|integer|min:50', // cents
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));

        // 1) Create customer
        $customer = $stripe->customers->create([
            'email' => $request->email,
            'name'  => $request->name,
        ]);

        // 2) Attach payment method to customer
        $stripe->paymentMethods->attach($request->payment_method, [
            'customer' => $customer->id,
        ]);

        // Optional but good practice: set default PM for invoices/future usage
        $stripe->customers->update($customer->id, [
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
        ]);

        // 3) Create + confirm PaymentIntent
        // ✅ Do NOT send confirmation_method when using automatic_payment_methods
        $pi = $stripe->paymentIntents->create([
            'amount'   => (int) $request->amount,
            'currency' => 'usd',

            'customer'       => $customer->id,
            'payment_method' => $request->payment_method,

            'confirm'       => true,   // attempt to pay now
            'off_session'   => false,  // user is present
            'receipt_email' => $request->email,

            'description' => 'One-time donation',
            'metadata'    => [
                'type' => 'general_donation_one_time',
            ],

            // ✅ Fix return_url error by blocking redirects
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => 'never',
            ],
        ]);

        $goal = FundRaisa::latest()->first();

        // 4) Save donation as pending first
        $donation = GeneralDonation::create([
            'fund_raisa_id'          => $goal?->id,
            'name'                   => $request->name,
            'email'                  => $request->email,
            'amount'                 => (int) $request->amount,
            'payment_id'             => $pi->id, // PaymentIntent id
            'donation_mode'          => 'stripe',
            'frequency'              => 'one_time',
            'stripe_customer_id'     => $customer->id,
            'stripe_subscription_id' => null,
            'status'                 => 'pending',
        ]);

        // 5) Handle statuses
        if (($pi->status ?? null) === 'succeeded') {
            $donation->update(['status' => 'paid']);

            return response()->json([
                'paid'             => true,
                'donation_id'       => $donation->id,
                'payment_intent_id' => $pi->id,
                'pi_status'         => $pi->status,
                'message'           => 'Paid successfully (no 3DS required).',
            ], 200);
        }

        // 3DS required -> client must confirm using client_secret
        if (in_array($pi->status, ['requires_action', 'requires_confirmation'], true)) {
            return response()->json([
                'paid'             => false,
                'donation_id'       => $donation->id,
                'payment_intent_id' => $pi->id,
                'client_secret'     => $pi->client_secret,
                'pi_status'         => $pi->status,
                'message'           => '3DS required. Please confirm payment on client. DB saved as pending.',
            ], 200);
        }

        // Any other status = failed for this flow
        $donation->update(['status' => 'failed']);

        return response()->json([
            'paid'             => false,
            'donation_id'       => $donation->id,
            'payment_intent_id' => $pi->id,
            'pi_status'         => $pi->status ?? null,
            'message'           => 'Payment failed.',
        ], 400);
    }
}
