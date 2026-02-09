<?php

namespace App\Http\Controllers;

use App\Mail\NewAlumniEventMail;
use App\Models\AlumniEvent;
use App\Models\AlumniMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AlumniEventsController extends Controller
{
    public function index()
    {
        $events = AlumniEvent::latest()->get();
        return view('dashboard.alumni.upcomingevents.index', compact('events'));
    }

    public function create()
    {
        return view('dashboard.alumni.upcomingevents.create');
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

        // Send new alumni event email to all alumni subscribers (queued to avoid timeout)
        $subscribers = AlumniMail::pluck('email')->filter()->unique();
        foreach ($subscribers as $email) {
            Mail::to($email)->queue(new NewAlumniEventMail($event));
        }
        // Notify admin
        Mail::to(config('mail.admin_email'))->queue(new NewAlumniEventMail($event));

        $message = $subscribers->isEmpty()
            ? 'Alumni Event created successfully.'
            : 'Alumni Event created successfully. Subscribers have been notified by email.';

        return redirect()
            ->route('alumniEvent.index')
            ->with('success', $message);
    }

    public function edit($id)
    {
        $event = AlumniEvent::findOrFail($id);
        return view('dashboard.alumni.upcomingevents.update', compact('event'));
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
