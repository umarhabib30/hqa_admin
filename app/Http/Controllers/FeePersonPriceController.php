<?php

namespace App\Http\Controllers;

use App\Models\feePersonPrice;
use App\Models\PtoEvents;
use Illuminate\Http\Request;

class FeePersonPriceController extends Controller
{
    public function index()
    {
        // Load the event relationship so we can show the event name in the table
        $fees = feePersonPrice::with('event')->latest()->get();
        return view('dashboard.pto.feePersonPrice.index', compact('fees'));
    }

    public function create()
    {
        $events = PtoEvents::all();
        return view('dashboard.pto.feePersonPrice.create', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id'  => 'required|exists:pto_events,id',
            'title'     => 'required|string',
            'price'     => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        // Optional: Deactivate other fees for this specific event if this one is active
        if ($request->has('is_active') && $request->is_active) {
            feePersonPrice::where('event_id', $request->event_id)->update(['is_active' => false]);
        }

        feePersonPrice::create($data);

        return redirect()->route('fee.index')->with('success', 'Fee created successfully.');
    }

    public function edit(feePersonPrice $fee)
    {
        $events = PtoEvents::all();
        return view('dashboard.pto.feePersonPrice.update', compact('fee', 'events'));
    }

    public function update(Request $request, feePersonPrice $fee)
    {
        $data = $request->validate([
            'event_id'  => 'required|exists:pto_events,id',
            'title'     => 'required|string',
            'price'     => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $fee->update($data);

        return redirect()->route('fee.index')->with('success', 'Fee updated successfully.');
    }

    public function destroy(feePersonPrice $fee)
    {
        $fee->delete();
        return back()->with('success', 'Fee deleted');
    }
}
