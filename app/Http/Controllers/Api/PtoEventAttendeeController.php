<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PtoEventAttendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PtoEventAttendeeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:pto_events,id',
        ]);

        $attendees = PtoEventAttendee::where('event_id', $request->event_id)
            ->latest()
            ->get();

        return response()->json($attendees);
    }

    public function createIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'email' => 'required|email',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => (int) round($request->amount * 100), // convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'email' => $request->email,
                    'type' => 'event_registration'
                ],
            ]);

            return response()->json([
                'status' => true,
                'clientSecret' => $intent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:pto_events,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'number_of_guests' => 'required|integer|min:0',
            'payment_id' => 'required|string',
            'amount' => 'required|numeric',
            'profile_pic' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $path = null;
            if ($request->hasFile('profile_pic')) {
                $path = $request->file('profile_pic')->store('profile_pics', 'public');
            }

            $attendee = PtoEventAttendee::create([
                'event_id' => $request->event_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'number_of_guests' => $request->number_of_guests,
                'amount' => $request->amount,
                'payment_id' => $request->payment_id,
                'profile_pic' => $path,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registration and payment successful',
                'data' => $attendee
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
