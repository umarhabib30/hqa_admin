<?php

namespace App\Http\Controllers;

use App\Models\Achievements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'card_title' => 'required|string|max:255',
            'card_price' => 'required|numeric|min:0',
            'card_percentage' => 'required|numeric|min:0|max:100',
            'card_desc' => 'nullable|array',
            'card_desc.*' => 'nullable|string',
        ]);

        if($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }


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

        // No validation here (per request). We still normalize/sanitize the payload.
        $cardDesc = $request->input('card_desc', []);
        // Backward compatibility: if a legacy textarea posted a single string, split by new lines.
        if (is_string($cardDesc)) {
            $cardDesc = preg_split("/\r\n|\n|\r/", $cardDesc) ?: [];
        }
        if (!is_array($cardDesc)) {
            $cardDesc = [];
        }

        $achievement->update([
            'main_title' => $request->main_title,
            'main_desc' => $request->main_desc,
            'card_title' => $request->card_title,
            'card_price' => $request->card_price,
            'card_percentage' => $request->card_percentage,
            'card_desc' => array_values(array_filter($cardDesc, fn ($v) => trim((string) $v) !== '')),
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
