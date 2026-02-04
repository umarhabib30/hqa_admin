<?php

namespace App\Http\Controllers;

use App\Models\AlumniEvent;
// use App\Models\AlumniSubscribeMail;
// use App\Mail\NewAlumniEventMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Mail;

class AlumniEventsController extends Controller
{
    public function index()
    {
        $events = AlumniEvent::latest()->get();
        return view('dashboard.alumni.upcomingEvents.index', compact('events'));
    }

    public function create()
    {
        return view('dashboard.alumni.upcomingEvents.create');
    }

    public function store(Request $request)
    {
        // ✅ VALIDATION
        $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'start_time'      => 'nullable|date_format:H:i',
            'end_time'        => 'nullable|date_format:H:i',
            'location'        => 'nullable|string|max:255',
            'organizer_name'  => 'nullable|string|max:255',
            'event_image'     => 'nullable|image',
            'organizer_logo'  => 'nullable|image',
        ]);

        // ✅ DATA PREP
        $data = $request->except(['event_image', 'organizer_logo']);

        // ✅ EVENT IMAGE UPLOAD
        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')
                ->store('alumni/events', 'public');
        }

        // ✅ ORGANIZER LOGO UPLOAD
        if ($request->hasFile('organizer_logo')) {
            $data['organizer_logo'] = $request->file('organizer_logo')
                ->store('alumni/organizers', 'public');
        }

        // ✅ CREATE EVENT
        $event = AlumniEvent::create($data);

        /*
        |--------------------------------------------------------------------------
        | MAIL PART (COMMENTED)
        |--------------------------------------------------------------------------
        |
        | $subscribers = AlumniSubscribeMail::pluck('email');
        |
        | foreach ($subscribers as $email) {
        |     Mail::to($email)->send(new NewAlumniEventMail($event));
        | }
        |
        */

        return redirect()
            ->route('alumniEvent.index')
            ->with('success', 'Alumni Event created successfully.');
    }

    public function edit($id)
    {
        $event = AlumniEvent::findOrFail($id);
        return view('dashboard.alumni.upcomingEvents.update', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = AlumniEvent::findOrFail($id);

        $data = $request->except(['event_image', 'organizer_logo']);

        if ($request->hasFile('event_image')) {
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = $request->file('event_image')
                ->store('alumni/events', 'public');
        }

        if ($request->hasFile('organizer_logo')) {
            if ($event->organizer_logo) {
                Storage::disk('public')->delete($event->organizer_logo);
            }
            $data['organizer_logo'] = $request->file('organizer_logo')
                ->store('alumni/organizers', 'public');
        }

        $event->update($data);

        return redirect()
            ->route('alumniEvent.index')
            ->with('success', 'Alumni Event updated successfully.');
    }

    public function destroy($id)
    {
        $event = AlumniEvent::findOrFail($id);

        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        if ($event->organizer_logo) {
            Storage::disk('public')->delete($event->organizer_logo);
        }

        $event->delete();

        return redirect()
            ->route('alumniEvent.index')
            ->with('success', 'Alumni Event deleted successfully.');
    }
}
