<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PtoEventAttendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:pto_events,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:pto_event_attendees,email',
            'phone' => 'required|string|max:20',
            'number_of_guests' => 'required|integer|min:0',
            'profile_pic' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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
            'profile_pic' => $path,
        ]);

        return response()->json([
            'message' => 'Form submitted successfully',
            'data' => $attendee
        ], 201);
    }
}
