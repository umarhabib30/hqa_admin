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

class SponserPackageApiController extends Controller
{
    /**
     * Create Stripe PaymentIntent
     */
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

    /**
     * Store new sponsor subscriber
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'user_email' => 'required|email',
            'payment_id' => 'required|string',
            'company_name' => 'nullable|string',
            'designation' => 'nullable|string',
            'user_phone' => 'nullable|string',
            'image' => 'nullable|image',
            'amount' => 'required|numeric',
            'sponsor_type' => 'required|string',
        ]);

        try {
            // Handle image upload if present
            $image = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('sponsor_package_subscribers', 'public');
            }

            // Find sponsor package
            $package = SponsorPackage::where('title', $request->sponsor_type)->first();
            if (!$package) {
                return response()->json(['status' => false, 'message' => 'Package not found'], 404);
            }

            // Create subscriber with company_name and designation
            $subscriber = SponserPackageSubscriber::create([
                'user_name' => $request->user_name,
                'user_email' => $request->user_email,
                'user_phone' => $request->user_phone,
                'company_name' => $request->company_name,   // ✅ added
                'designation' => $request->designation,    // ✅ added
                'sponsor_package_id' => $package->id,
                'sponsor_type' => $request->sponsor_type,
                'image' => $image,
                'amount' => $request->amount,
                'payment_id' => $request->payment_id,
                'status' => 'paid',
            ]);

            try {
                $subscriber->load('package');

                // Send confirmation to subscriber
                Mail::to($subscriber->user_email)->queue(new SponsorSubscriberConfirmationMail($subscriber));

                // Notify admin
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

    public function packages()
    {
        $packages = SponsorPackage::all();
        return response()->json([
            'status' => true,
            'data' => $packages
        ]);
    }
}
