<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SponsorSubscriberConfirmationMail;
use App\Mail\SponsorSubscriberCreatedMail;
use Illuminate\Http\Request;
use App\Models\SponserPackageSubscriber;
use App\Models\SponsorPackage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class SponserApiSubscriber extends Controller
{

    public function createIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'user_email' => 'required|email',
            'sponsor_type' => 'required|string',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => (int) round($request->amount * 100),
                'currency' => 'usd',
                'metadata' => [
                    'email' => $request->user_email,
                    'sponsor_type' => $request->sponsor_type,
                ],
            ]);

            return response()->json([
                'status' => true,
                'clientSecret' => $intent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe Intent Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Could not initialize payment: ' . $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'user_email' => 'required|email',
            'payment_id' => 'required|string',
        ]);

        try {
            $image = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('sponsor_package_subscribers', 'public');
            }

            $package = SponsorPackage::where('title', $request->sponsor_type)->first();

            if (!$package) {
                return response()->json(['status' => false, 'message' => 'Package not found'], 404);
            }

            $subscriber = SponserPackageSubscriber::create([
                'user_name' => $request->user_name,
                'user_email' => $request->user_email,
                'user_phone' => $request->user_phone,
                'sponsor_package_id' => $package->id,
                'sponsor_type' => $request->sponsor_type,
                'image' => $image,
                'amount' => $request->amount,
                'payment_id' => $request->payment_id,
                'status' => 'paid',
            ]);

            try {
                $subscriber->load('package');
                // Confirmation to the subscriber
                Mail::to($subscriber->user_email)->queue(new SponsorSubscriberConfirmationMail($subscriber));
                // Admin notification
                Mail::to(config('mail.admin_email'))->queue(new SponsorSubscriberCreatedMail($subscriber));
            } catch (\Throwable $e) {
                Log::warning('Sponsor subscriber email failed', [
                    'subscriber_id' => $subscriber->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Sponsorship confirmed and recorded successfully',
                'data' => $subscriber
            ], 201);
        } catch (\Exception $e) {
            Log::error('Database Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }
}
