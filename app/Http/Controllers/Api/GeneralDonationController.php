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
            'donation_for' => 'required|string|max:255', // ✅ NEW
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

        $getInvoice = function () use ($stripe, $invoiceId) {
            return $stripe->invoices->retrieve($invoiceId, [
                'expand' => ['payment_intent', 'charge'],
            ]);
        };

        $invoice = $getInvoice();

        if (($invoice->status ?? null) === 'draft') {
            $stripe->invoices->finalizeInvoice($invoiceId, []);
            $invoice = $getInvoice();
        }

        if (($invoice->status ?? null) === 'open') {
            $stripe->invoices->pay($invoiceId, [
                'payment_method' => $request->payment_method,
            ]);
            $invoice = $getInvoice();
        }

        $subscriptionFresh = $stripe->subscriptions->retrieve($subscription->id, []);

        if (($invoice->status ?? null) === 'paid') {
            $goal = FundRaisa::latest()->first();

            // Store donation with donation_for
            $donation = GeneralDonation::create([
                'fund_raisa_id' => $goal->id,
                'donation_for' => $request->donation_for, // ✅ NEW
                'name' => $request->name,
                'email' => $request->email,
                'amount' => (int) $request->amount,
                'payment_id' => $invoiceId,
                'donation_mode' => 'stripe',
                'frequency' => $request->interval,
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
                'charge_id' => $invoice->charge->id ?? $invoice->charge ?? null,
                'message' => 'Invoice is already paid. No client_secret required.',
            ], 200);
        }

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
            'amount'         => 'required|integer|min:50',
            'donation_for'   => 'required|string|max:255', // ✅ NEW
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));

        $customer = $stripe->customers->create([
            'email' => $request->email,
            'name'  => $request->name,
        ]);

        $stripe->paymentMethods->attach($request->payment_method, [
            'customer' => $customer->id,
        ]);

        $stripe->customers->update($customer->id, [
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
        ]);

        $pi = $stripe->paymentIntents->create([
            'amount'   => (int) $request->amount,
            'currency' => 'usd',
            'customer'       => $customer->id,
            'payment_method' => $request->payment_method,
            'confirm'       => true,
            'off_session'   => false,
            'receipt_email' => $request->email,
            'description' => 'One-time donation',
            'metadata'    => [
                'type' => 'general_donation_one_time',
            ],
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => 'never',
            ],
        ]);

        $goal = FundRaisa::latest()->first();

        $donation = GeneralDonation::create([
            'fund_raisa_id'          => $goal?->id,
            'donation_for'           => $request->donation_for, // ✅ NEW
            'name'                   => $request->name,
            'email'                  => $request->email,
            'amount'                 => (int) $request->amount,
            'payment_id'             => $pi->id,
            'donation_mode'          => 'stripe',
            'frequency'              => 'one_time',
            'stripe_customer_id'     => $customer->id,
            'stripe_subscription_id' => null,
            'status'                 => 'pending',
        ]);

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
