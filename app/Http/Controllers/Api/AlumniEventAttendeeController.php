<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AlumniEventAttendeeConfirmationMail;
use App\Models\AlumniEventAttendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Throwable;

class AlumniEventAttendeeController extends Controller
{
    /**
     * GET attendees by alumni event
     * /api/alumni-event-attendees?event_id=1
     */
    public function index(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:alumni_events,id',
        ]);

        $attendees = AlumniEventAttendee::where('event_id', $request->event_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $attendees
        ], 200);
    }

    /**
     * Create Stripe Payment Intent
     */
    public function createIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'email'  => 'required|email',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount'   => (int) round($request->amount * 100),
                'currency' => 'usd',
                'metadata' => [
                    'email' => $request->email,
                    'type'  => 'alumni_event_registration'
                ],
            ]);

            return response()->json([
                'status'       => true,
                'clientSecret' => $intent->client_secret,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create payment intent',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store Alumni Event Attendee
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id'         => 'required|exists:alumni_events,id',
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email',
            'phone'            => 'required|string|max:20',
            'number_of_guests' => 'required|integer|min:0',
            'payment_id'       => 'required|string',
            'amount'           => 'required|numeric',
            'profile_pic'      => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $path = null;
            if ($request->hasFile('profile_pic')) {
                $path = $request->file('profile_pic')
                    ->store('alumni_profile_pics', 'public');
            }

            $attendee = AlumniEventAttendee::create([
                'event_id'         => $request->event_id,
                'first_name'       => $request->first_name,
                'last_name'        => $request->last_name,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'number_of_guests' => $request->number_of_guests,
                'amount'           => $request->amount,
                'payment_id'       => $request->payment_id,
                'profile_pic'      => $path,
            ]);

            try {
                Mail::to($attendee->email)->queue(new AlumniEventAttendeeConfirmationMail($attendee));
            } catch (Throwable $e) {
                // Log but don't fail the request
            }

            return response()->json([
                'status'  => true,
                'message' => 'Alumni event registration successful',
                'data'    => $attendee,
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to store attendee',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
