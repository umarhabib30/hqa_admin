<?php

namespace App\Http\Controllers;

use App\Models\Achievements;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $achievements = Achievements::all();
        return view('dashboard.donation.Achievements.index', compact('achievements'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.donation.Achievements.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'main_title' => 'nullable|string|max:255',
            'main_desc' => 'nullable|string',
            'card_title' => 'required|string|max:255',
            'card_price' => 'required|numeric|min:0',
            'card_percentage' => 'required|numeric|min:0|max:100',
            'card_desc' => 'nullable|array',
            'card_desc.*' => 'nullable|string',
        ]);

        Achievements::create([
            'main_title' => $request->main_title,
            'main_desc' => $request->main_desc,
            'card_title' => $request->card_title,
            'card_price' => $request->card_price,
            'card_percentage' => $request->card_percentage,
            'card_desc' => array_values(array_filter($request->card_desc ?? [])),
        ]);

        return redirect()
            ->route('achievements.index')
            ->with('success', 'Achievement created successfully!');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $achievement = Achievements::findOrFail($id);
        return view('dashboard.donation.Achievements.update', compact('achievement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $achievement = Achievements::findOrFail($id);

        $request->validate([
            'main_title' => 'nullable|string|max:255',
            'main_desc' => 'nullable|string',
            'card_title' => 'required|string|max:255',
            'card_price' => 'required|numeric|min:0',
            'card_percentage' => 'required|numeric|min:0|max:100',
            'card_desc' => 'nullable|array',
            'card_desc.*' => 'nullable|string',
        ]);

        $achievement->update([
            'main_title' => $request->main_title,
            'main_desc' => $request->main_desc,
            'card_title' => $request->card_title,
            'card_price' => $request->card_price,
            'card_percentage' => $request->card_percentage,
            'card_desc' => array_values(array_filter($request->card_desc ?? [])),
        ]);


        return redirect()
            ->route('achievements.index')
            ->with('success', 'Achievement updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $achievement = Achievements::findOrFail($id);

        $achievement->delete();

        return redirect()
            ->route('achievements.index')
            ->with('success', 'Achievement deleted successfully!');
    }
}
