<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\GeneralDonation; 
use App\Models\FundRaisa;
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
            'donation_mode' => 'nullable|in:paid_now,pledged',
            'payment_id' => 'required_if:donation_mode,paid_now|nullable|string',
        ]);

        try {
            $latestGoalId = FundRaisa::latest('id')->value('id');

            $donation = GeneralDonation::create([
                'fund_raisa_id' => $latestGoalId,
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'amount'     => $validated['amount'],
                'payment_id' => $validated['payment_id'] ?? null,
                'donation_mode' => $validated['donation_mode'] ?? 'paid_now',
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
