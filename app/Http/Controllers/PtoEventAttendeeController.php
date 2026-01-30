<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PtoEventAttendee;
use App\Models\PtoEvents;
use Illuminate\Http\Request;

class PtoEventAttendeeController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all events for dropdown
        $events = PtoEvents::all();

        // Fetch attendee counts per event
        $attendeeCounts = PtoEventAttendee::selectRaw('event_id, COUNT(*) as total')
            ->groupBy('event_id')
            ->pluck('total', 'event_id'); // returns [event_id => count]

        // Base query for attendees
        $query = PtoEventAttendee::with('event');

        if ($request->has('event_id') && $request->event_id != '') {
            $query->where('event_id', $request->event_id);
        }

        $attendees = $query->latest()->get();

        return view('dashboard.pto_event_attendees.index', compact('attendees', 'events', 'attendeeCounts'));
    }

    public function destroy($id)
    {
        $attendee = PtoEventAttendee::findOrFail($id);

        if ($attendee->profile_pic && file_exists(storage_path('app/public/' . $attendee->profile_pic))) {
            unlink(storage_path('app/public/' . $attendee->profile_pic));
        }

        $attendee->delete();

        return redirect()->back()->with('success', 'Attendee deleted successfully.');
    }
}
