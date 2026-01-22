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
        $validated = $request->validate([
            'goal_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'starting_goal' => 'nullable|numeric|min:0',
            'ending_goal' => 'nullable|numeric|min:0',
        ]);

        // Keep total_donors in DB but don't take it from form
        $validated['total_donors'] = 0;

        FundRaisa::create($validated);

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

        $validated = $request->validate([
            'goal_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'starting_goal' => 'nullable|numeric|min:0',
            'ending_goal' => 'nullable|numeric|min:0',
        ]);

        // total_donors remains unchanged via edit form
        $fundRaise->update($validated);

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
