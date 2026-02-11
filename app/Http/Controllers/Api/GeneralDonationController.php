<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\GeneralDonationConfirmationMail;
use App\Mail\GeneralDonationReceivedMail;
use App\Models\FundRaisa;
use App\Models\GeneralDonation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

class GeneralDonationController extends Controller
{
    public function show()
    {
        return view('subscribe_dynamic');
    }

    // Recurring donation
    public function recurringDonation(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'email'          => 'required|email',
            'name'           => 'nullable|string',
            'amount'         => 'required|integer|min:0',
            'interval'       => 'required|string|in:month,year',
            'donation_for'   => 'required|string|max:255',
            'address1'       => 'required|string|max:255',
            'address2'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'state'          => 'required|string|max:255',
            'country'        => 'required|string|max:255',
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));

        // Create Stripe Customer
        $customer = $stripe->customers->create([
            'email' => $request->email,
            'name'  => $request->name,
            'metadata' => [
        'purpose' => $request->donation_for, // Add this for consistency
    ],
        ]);

        $stripe->paymentMethods->attach($request->payment_method, [
            'customer' => $customer->id,
        ]);

        $stripe->customers->update($customer->id, [
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
        ]);

        // Create Product & Price
        $product = $stripe->products->create(['name' => 'HQA Funding']);
        $price   = $stripe->prices->create([
            'unit_amount' => (int) $request->amount * 100,
            'currency'    => 'usd',
            'recurring'   => ['interval' => $request->interval],
            'product'     => $product->id,
        ]);

        // Create Subscription
        $subscription = $stripe->subscriptions->create([
            'customer' => $customer->id,
            'items'    => [['price' => $price->id]],
            'metadata' => [
                'purpose' => $request->donation_for, // <--- Add this
            ],
            'collection_method' => 'charge_automatically',
            'payment_behavior'  => 'default_incomplete',
            'payment_settings'  => [
                'save_default_payment_method' => 'on_subscription',
                'payment_method_types'        => ['card'],
            ],
            'expand' => ['latest_invoice'],
        ]);

        $invoiceId = is_string($subscription->latest_invoice)
            ? $subscription->latest_invoice
            : ($subscription->latest_invoice->id ?? null);

        $invoice = $stripe->invoices->retrieve($invoiceId, ['expand' => ['payment_intent', 'charge']]);

        if (($invoice->status ?? null) === 'draft') {
            $stripe->invoices->finalizeInvoice($invoiceId);
            $invoice = $stripe->invoices->retrieve($invoiceId, ['expand' => ['payment_intent', 'charge']]);
        }

        if (($invoice->status ?? null) === 'open') {
            $stripe->invoices->pay($invoiceId, ['payment_method' => $request->payment_method]);
            $invoice = $stripe->invoices->retrieve($invoiceId, ['expand' => ['payment_intent', 'charge']]);
        }

        $subscriptionFresh = $stripe->subscriptions->retrieve($subscription->id);

        if (($invoice->status ?? null) === 'paid') {
            $goal = FundRaisa::latest()->first();

            $donation = GeneralDonation::create([
                'fund_raisa_id'          => $goal->id,
                'donation_for'           => $request->donation_for,
                'name'                   => $request->name,
                'email'                  => $request->email,
                'amount'                 => (int) $request->amount,
                'payment_id'             => $invoiceId,
                'donation_mode'          => 'stripe',
                'frequency'              => $request->interval,
                'stripe_customer_id'     => $customer->id,
                'stripe_subscription_id' => $subscription->id,
                'status'                 => 'paid',
                'address1'               => $request->address1,
                'address2'               => $request->address2,
                'city'                   => $request->city,
                'state'                  => $request->state,
                'country'                => $request->country,
            ]);

            $this->sendDonationEmails($donation);

            return response()->json([
                'paid'                => true,
                'donation_id'         => $donation->id,
                'subscription_id'     => $subscription->id,
                'subscription_status' => $subscriptionFresh->status,
                'latest_invoice_id'   => $invoiceId,
                'invoice_status'      => $invoice->status,
            ], 200);
        }

        $pi = $invoice->payment_intent ?? null;
        if (is_string($pi)) $pi = $stripe->paymentIntents->retrieve($pi);

        return response()->json([
            'paid' => false,
            'client_secret' => $pi->client_secret ?? null,
            'pi_status' => $pi->status ?? null,
            'subscription_status' => $subscriptionFresh->status ?? $subscription->status,
        ], 200);
    }

    // One-time donation
    public function oneTimeDonation(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'email'          => 'required|email',
            'name'           => 'nullable|string',
            'amount'         => 'required|integer|min:0',
            'donation_for'   => 'required|string|max:255',
            'fund_raisa_id'  => 'nullable|integer|exists:fund_raisas,id',
            'address1'       => 'required|string|max:255',
            'address2'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'state'          => 'required|string|max:255',
            'country'        => 'required|string|max:255',
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));

        $customer = $stripe->customers->create([
            'email' => $request->email,
            'name'  => $request->name,
            'metadata' => [
                'purpose' => $request->donation_for, // <--- Add this
            ],
        ]);

        $stripe->paymentMethods->attach($request->payment_method, ['customer' => $customer->id]);
        $stripe->customers->update($customer->id, [
            'invoice_settings' => ['default_payment_method' => $request->payment_method]
        ]);

        $pi = $stripe->paymentIntents->create([
            'amount'   => (int) $request->amount * 100,
            'currency' => 'usd',
            'customer' => $customer->id,
            'payment_method' => $request->payment_method,
            'confirm'  => true,
            'off_session' => false,
            'receipt_email' => $request->email,
            'description'   => 'Donation for: ' . $request->donation_for, // Also added to description
            'metadata'      => [
                'type'    => 'general_donation_one_time',
                'purpose' => $request->donation_for, // <--- Add this
            ],
            // 'description' => 'One-time donation',
            // 'metadata'    => ['type' => 'general_donation_one_time'],
            'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
        ]);

        $goalId = $request->filled('fund_raisa_id')
            ? (int) $request->fund_raisa_id
            : FundRaisa::latest('id')->value('id');

        $donation = GeneralDonation::create([
            'fund_raisa_id'          => $goalId,
            'donation_for'           => $request->donation_for,
            'name'                   => $request->name,
            'email'                  => $request->email,
            'amount'                 => (int) $request->amount,
            'payment_id'             => $pi->id,
            'donation_mode'          => 'stripe',
            'frequency'              => 'one_time',
            'stripe_customer_id'     => $customer->id,
            'stripe_subscription_id' => null,
            'status'                 => 'pending',
            'address1'               => $request->address1,
            'address2'               => $request->address2,
            'city'                   => $request->city,
            'state'                  => $request->state,
            'country'                => $request->country,
        ]);

        if ($pi->status === 'succeeded') {
            $donation->update(['status' => 'paid']);
            $this->sendDonationEmails($donation);
            return response()->json([
                'paid'             => true,
                'donation_id'      => $donation->id,
                'payment_intent_id' => $pi->id,
                'pi_status'        => $pi->status,
            ], 200);
        }

        if (in_array($pi->status, ['requires_action', 'requires_confirmation'])) {
            return response()->json([
                'paid'             => false,
                'donation_id'      => $donation->id,
                'payment_intent_id' => $pi->id,
                'client_secret'    => $pi->client_secret,
                'pi_status'        => $pi->status,
            ], 200);
        }

        $donation->update(['status' => 'failed']);
        return response()->json([
            'paid'             => false,
            'donation_id'      => $donation->id,
            'payment_intent_id' => $pi->id,
            'pi_status'        => $pi->status,
        ], 400);
    }

    /**
     * Send confirmation to donor and notification to super admins.
     */
    private function sendDonationEmails(GeneralDonation $donation): void
    {
        if (!empty($donation->email)) {
            Mail::to($donation->email)->queue(new GeneralDonationConfirmationMail($donation));
        }

        $superAdmins = User::where('role', 'super_admin')->get();
        foreach ($superAdmins as $admin) {
            if (!empty($admin->email)) {
                Mail::to($admin->email)->queue(new GeneralDonationReceivedMail($donation));
            }
        }
    }
}
