<?php

namespace App\Http\Controllers;

use App\Models\feePersonPrice;
use Illuminate\Http\Request;

class FeePersonPriceController extends Controller
{
    public function index()
    {
        $fees = feePersonPrice::latest()->get();
        return view('dashboard.pto.feePersonPrice.index', compact('fees'));
    }

    public function create()
    {
        return view('dashboard.pto.feePersonPrice.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        feePersonPrice::create($data);

        return redirect()->route('fee.index')
            ->with('success', 'Fee created');
    }

    public function edit(FeePersonPrice $fee)
    {
        return view('dashboard.pto.feePersonPrice.update', compact('fee'));
    }

    public function update(Request $request, FeePersonPrice $fee)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $fee->update($data);

        return redirect()->route('fee.index')
            ->with('success', 'Fee updated');
    }

    public function destroy(FeePersonPrice $fee)
    {
        $fee->delete();

        return back()->with('success', 'Fee deleted');
    }
}
