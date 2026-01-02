<?php

namespace App\Http\Controllers;

use App\Models\Calender;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $events = Calender::all();
        return view('dashboard.calender.index', compact('events'));
    }

    public function create()
    {
        return view('dashboard.calender.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Calender::create($request->all());

        return redirect()->route('calender.index')
            ->with('success', 'Event created successfully');
    }

    public function edit($id)
    {
        $event = Calender::findOrFail($id);
        return view('dashboard.calender.update', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = Calender::findOrFail($id);
        $event->update($request->all());

        return redirect()->route('calender.index')
            ->with('success', 'Event updated successfully');
    }

    public function destroy($id)
    {
        Calender::findOrFail($id)->delete();
        return back()->with('success', 'Event deleted');
    }
}
