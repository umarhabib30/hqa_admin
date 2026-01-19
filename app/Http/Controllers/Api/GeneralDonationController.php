<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\GeneralDonation; 
use Illuminate\Support\Facades\Log;

class GeneralDonationController extends Controller
{
  
    public function processDonation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string',
            'email'  => 'required|email',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => (int) ($validated['amount'] * 100), 
                'currency' => 'usd',
                'metadata' => [
                    'donor_name' => $validated['name'],
                    'donor_email' => $validated['email'],
                ],
            ]);

            return response()->json([
                'success' => true,
                'clientSecret' => $intent->client_secret,
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe Intent Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

 
    public function confirmDonation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email',
            'amount'     => 'required|numeric',
            'payment_id' => 'required|string', 
        ]);

        try {
            $donation = GeneralDonation::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'amount'     => $validated['amount'],
                'payment_id' => $validated['payment_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Donation saved successfully!',
                'data'    => $donation
            ]);
        } catch (\Throwable $e) {
            Log::error('Database Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment succeeded but failed to save to database.'
            ], 500);
        }
    }
}
