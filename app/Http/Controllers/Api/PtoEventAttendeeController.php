<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PtoEventAttendee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PtoEventAttendeeController extends Controller
{
    // Store form submission
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:pto_event_attendees,email',
            'phone'      => 'required|string|max:20',
            // 'will_attend'=> 'required|boolean',
            'number_of_guests' => 'required|integer|min:0',
            'profile_pic' => 'nullable|image|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle profile picture upload
        $path = null;
        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        // Create attendee
        $attendee = PtoEventAttendee::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            // 'will_attend' => $request->will_attend,
            'number_of_guests' => $request->number_of_guests,
            'profile_pic' => $path,
        ]);

        return response()->json([
            'message' => 'Form submitted successfully',
            'data' => $attendee
        ], 201);
    }

    // Fetch all attendees
    public function index()
    {
        $attendees = PtoEventAttendee::all();
        return response()->json($attendees);
    }
}
