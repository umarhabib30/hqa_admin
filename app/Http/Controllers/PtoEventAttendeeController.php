<?php

namespace App\Http\Controllers;

use App\Models\PtoEventAttendee;
use Illuminate\Http\Request;

class PtoEventAttendeeController extends Controller
{
    /**
     * Display a listing of the PTO event attendees.
     */
    public function index()
    {
        // Fetch all attendees latest first
        $attendees = PtoEventAttendee::latest()->get();

        // Return the Blade view
        return view('dashboard.pto_event_attendees.index', compact('attendees'));
    }

    /**
     * Remove the specified attendee from storage.
     */
    public function destroy($id)
    {
        $attendee = PtoEventAttendee::findOrFail($id);
        $attendee->delete();

        return redirect()->back()->with('success', 'Attendee deleted successfully');
    }
}
