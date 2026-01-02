<?php

namespace App\Http\Controllers;

use App\Models\EasyJoin;
use App\Models\feePersonPrice;
use Illuminate\Http\Request;

class EasyJoinController extends Controller
{
    // LIST
    public function index()
    {
        $joins = EasyJoin::latest()->get();
        return view('dashboard.pto.easyjoin.index', compact('joins'));
    }

    // CREATE FORM
    public function create()
    {
        $fee = feePersonPrice::where('is_active', true)->first();

        return view('dashboard.pto.easyjoin.create', compact('fee'));
    }

    // STORE
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'email'        => 'required|email|unique:easy_joins,email',
            'is_attending' => 'required|in:yes,no',
            'guest_count'  => 'required|integer|min:1', // total people
        ]);

        $fee = feePersonPrice::where('is_active', true)->firstOrFail();

        if ($data['is_attending'] === 'no') {
            $data['fee_per_person'] = 0;
            $data['total_fee'] = 0;
        } else {
            // ✅ SIMPLE & CORRECT
            $data['fee_per_person'] = $fee->price;
            $data['total_fee'] = $data['guest_count'] * $fee->price;
        }

        $data['fee_person_price_id'] = $fee->id;

        EasyJoin::create($data);

        return redirect()->route('easy-joins.index')
            ->with('success', 'Record created successfully');
    }

    // SHOW
    public function show(EasyJoin $easyJoin)
    {
        return view('easy-joins.show', compact('easyJoin'));
    }

    // EDIT FORM
    public function edit(EasyJoin $easyJoin)
    {
        return view('dashboard.pto.easyjoin.update', compact('easyJoin'));
    }

    // UPDATE
    public function update(Request $request, EasyJoin $easyJoin)
    {
        $data = $request->validate([
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'email'        => 'required|email|unique:easy_joins,email,' . $easyJoin->id,
            'is_attending' => 'required|in:yes,no',
            'guest_count'  => 'required|integer|min:1',
        ]);

        $fee = feePersonPrice::where('is_active', true)->firstOrFail();

        if ($data['is_attending'] === 'no') {
            $data['fee_per_person'] = 0;
            $data['total_fee'] = 0;
        } else {
            $data['fee_per_person'] = $fee->price;
            $data['total_fee'] = $data['guest_count'] * $fee->price;
        }

        $data['fee_person_price_id'] = $fee->id;

        $easyJoin->update($data);

        return redirect()->route('easy-joins.index')
            ->with('success', 'Record updated successfully');
    }


    // DELETE
    public function destroy(EasyJoin $easyJoin)
    {
        $easyJoin->delete();

        return redirect()->route('easy-joins.index')
            ->with('success', 'Record deleted successfully');
    }
}
