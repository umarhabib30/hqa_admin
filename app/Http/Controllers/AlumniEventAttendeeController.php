<?php

namespace App\Http\Controllers;

use App\Models\AlumniEvent;
use App\Models\AlumniEventAttendee;
use Illuminate\Http\Request;

class AlumniEventAttendeeController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all alumni events for dropdown
        $events = AlumniEvent::all();

        // Fetch attendee counts per event
        $attendeeCounts = AlumniEventAttendee::selectRaw('event_id, COUNT(*) as total')
            ->groupBy('event_id')
            ->pluck('total', 'event_id'); // [event_id => count]

        // Base query with event relation
        $query = AlumniEventAttendee::with('event');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $attendees = $query->latest()->get();

        return view(
            'dashboard.alumni_event_attendees.index',
            compact('attendees', 'events', 'attendeeCounts')
        );
    }

    public function destroy($id)
    {
        $attendee = AlumniEventAttendee::findOrFail($id);

        if (
            $attendee->profile_pic &&
            file_exists(storage_path('app/public/' . $attendee->profile_pic))
        ) {
            unlink(storage_path('app/public/' . $attendee->profile_pic));
        }

        $attendee->delete();

        return redirect()
            ->back()
            ->with('success', 'Attendee deleted successfully.');
    }
}
