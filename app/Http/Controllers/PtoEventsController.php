<?php

namespace App\Http\Controllers;

use App\Mail\NewPtoEventMail;
use App\Models\PtoEvents as PtoEvent;
use App\Models\PtoEvents;
use App\Models\PtoSubscribeMails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PtoEventsController extends Controller
{
    public function index()
    {
        $events = PtoEvent::latest()->get();
        return view('dashboard.pto.upcomingEvents.index', compact('events'));
    }

    public function create()
    {
        return view('dashboard.pto.upcomingEvents.create');
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
                ->store('pto/events', 'public');
        }

        // ✅ ORGANIZER LOGO UPLOAD
        if ($request->hasFile('organizer_logo')) {
            $data['organizer_logo'] = $request->file('organizer_logo')
                ->store('pto/organizers', 'public');
        }

        // ✅ CREATE EVENT
        $event = PtoEvents::create($data);

        // ✅ SEND EMAIL TO ALL PTO SUBSCRIBERS
        $subscribers = PtoSubscribeMails::pluck('email');

        foreach ($subscribers as $email) {
            Mail::to($email)->send(new NewPtoEventMail($event));
        }

        // ✅ REDIRECT
        return redirect()
            ->route('ptoEvents.index')
            ->with('success', 'PTO Event created successfully and emails sent to subscribers.');
    }


    public function edit($id)
    {
        $event = PtoEvent::findOrFail($id);
        return view('dashboard.pto.upcomingEvents.update', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = PtoEvent::findOrFail($id);

        $data = $request->except(['event_image', 'organizer_logo']);

        // EVENT IMAGE UPDATE
        if ($request->hasFile('event_image')) {
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = $request->file('event_image')
                ->store('pto/events', 'public');
        }

        // ORGANIZER LOGO UPDATE
        if ($request->hasFile('organizer_logo')) {
            if ($event->organizer_logo) {
                Storage::disk('public')->delete($event->organizer_logo);
            }
            $data['organizer_logo'] = $request->file('organizer_logo')
                ->store('pto/organizers', 'public');
        }

        $event->update($data);

        return redirect()
            ->route('ptoEvents.index')
            ->with('success', 'PTO Event updated successfully');
    }

    public function destroy($id)
    {
        $event = PtoEvent::findOrFail($id);

        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        if ($event->organizer_logo) {
            Storage::disk('public')->delete($event->organizer_logo);
        }

        $event->delete();

        return redirect()
            ->route('ptoEvents.index')
            ->with('success', 'PTO Event deleted successfully');
    }
}
