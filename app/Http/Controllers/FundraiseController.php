<?php

namespace App\Http\Controllers;

use App\Models\FundRaisa;
use Illuminate\Http\Request;

class FundraiseController extends Controller
{
    public function index()
    {
        $fundRaises = FundRaisa::latest()->get();
        return view('dashboard.donation.fundraiserGoals.index', compact('fundRaises'));
    }

    public function create()
    {
        return view('dashboard.donation.fundraiserGoals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'starting_goal' => 'required|numeric|min:0',
            'ending_goal'   => 'required|numeric|min:0',
            'total_donors'  => 'required|numeric|min:0',
        ]);

        FundRaisa::create($request->all());

        return redirect()
            ->route('fundRaise.index')
            ->with('success', 'FundRaise goal created successfully!');
    }

    public function edit(string $id)
    {
        $fundRaise = FundRaisa::findOrFail($id);
        return view('dashboard.donation.fundraiserGoals.update', compact('fundRaise'));
    }

    public function update(Request $request, string $id)
    {
        $fundRaise = FundRaisa::findOrFail($id);

        $request->validate([
            'starting_goal' => 'required|numeric|min:0',
            'ending_goal'   => 'required|numeric|min:0',
            'total_donors'  => 'required|numeric|min:0',
        ]);

        $fundRaise->update($request->all());

        return redirect()
            ->route('fundRaise.index')
            ->with('success', 'FundRaise goal updated successfully!');
    }

    public function destroy(string $id)
    {
        FundRaisa::findOrFail($id)->delete();

        return redirect()
            ->route('fundRaise.index')
            ->with('success', 'FundRaise goal deleted successfully!');
    }
}
