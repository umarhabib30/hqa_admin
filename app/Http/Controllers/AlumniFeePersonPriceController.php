<?php

namespace App\Http\Controllers;

use App\Models\AlumniFeePersonPrice;
use App\Models\AlumniEvent;
use Illuminate\Http\Request;

class AlumniFeePersonPriceController extends Controller
{
    public function index()
    {
        $fees = AlumniFeePersonPrice::with('event')->latest()->get();
        return view('dashboard.alumni.feePersonPrice.index', compact('fees'));
    }

    public function create()
    {
        $events = AlumniEvent::all();
        return view('dashboard.alumni.feePersonPrice.create', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id'  => 'required|exists:alumni_events,id',
            'title'     => 'required|string',
            'price'     => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        // If this fee is active, deactivate other fees for the event
        if ($request->has('is_active') && $request->is_active) {
            AlumniFeePersonPrice::where('event_id', $request->event_id)->update(['is_active' => false]);
        }

        AlumniFeePersonPrice::create($data);

        return redirect()->route('alumniFee.index')->with('success', 'Fee created successfully.');
    }

    public function edit(AlumniFeePersonPrice $fee)
    {
        $events = AlumniEvent::all();
        return view('dashboard.alumni.feePersonPrice.update', compact('fee', 'events'));
    }

    public function update(Request $request, AlumniFeePersonPrice $fee)
    {
        $data = $request->validate([
            'event_id'  => 'required|exists:alumni_events,id',
            'title'     => 'required|string',
            'price'     => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $fee->update($data);

        return redirect()->route('alumniFee.index')->with('success', 'Fee updated successfully.');
    }

    public function destroy(AlumniFeePersonPrice $fee)
    {
        $fee->delete();
        return back()->with('success', 'Fee deleted');
    }
}
