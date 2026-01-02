<?php

namespace App\Http\Controllers;

use App\Models\AlumniEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AlumniMail;
use App\Mail\NewAlumniEventMail;
use Illuminate\Support\Facades\Mail;

class AlumniEventController extends Controller
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',

            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            'start_time' => 'nullable',
            'end_time' => 'nullable',

            'location' => 'nullable|string|max:255',
            'organizer_name' => 'nullable|string|max:255',

            'organizer_logo' => 'nullable|image',
            'event_image' => 'nullable|image',
        ]);

        // ✅ DATA PREP
        $data = $request->except(['organizer_logo', 'event_image']);

        // ✅ ORGANIZER LOGO
        if ($request->hasFile('organizer_logo')) {
            $data['organizer_logo'] = $request->file('organizer_logo')
                ->store('alumni/events/organizers', 'public');
        }

        // ✅ EVENT IMAGE
        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')
                ->store('alumni/events', 'public');
        }

        // ✅ CREATE EVENT
        $event = AlumniEvent::create($data);

        // 🔔 SEND EMAIL TO ALL ALUMNI SUBSCRIBERS
        $subscribers = AlumniMail::pluck('email');

        foreach ($subscribers as $email) {
            Mail::to($email)->send(new NewAlumniEventMail($event));
        }

        return redirect()
            ->route('alumniEvent.index')
            ->with('success', 'Alumni Event created successfully and emails sent to subscribers.');
    }

    public function edit($id)
    {
        $event = AlumniEvent::findOrFail($id);
        return view('dashboard.alumni.upcomingevents.update', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = AlumniEvent::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',

            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            'start_time' => 'nullable',
            'end_time' => 'nullable',

            'location' => 'nullable|string|max:255',
            'organizer_name' => 'nullable|string|max:255',

            'organizer_logo' => 'nullable|image',
            'event_image' => 'nullable|image',
        ]);

        $data = $request->except(['organizer_logo', 'event_image']);

        if ($request->hasFile('organizer_logo')) {
            if ($event->organizer_logo) {
                Storage::disk('public')->delete($event->organizer_logo);
            }
            $data['organizer_logo'] = $request->file('organizer_logo')
                ->store('alumni/events/organizers', 'public');
        }

        if ($request->hasFile('event_image')) {
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = $request->file('event_image')
                ->store('alumni/events', 'public');
        }

        $event->update($data);

        return redirect()
            ->route('alumniEvent.index')
            ->with('success', 'Alumni Event updated successfully');
    }

    public function destroy($id)
    {
        $event = AlumniEvent::findOrFail($id);

        if ($event->organizer_logo) {
            Storage::disk('public')->delete($event->organizer_logo);
        }

        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        $event->delete();

        return back()->with('success', 'Alumni Event deleted');
    }
}
