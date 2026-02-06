<?php

namespace App\Http\Controllers;

use App\Models\homeMemories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeMemoriesController extends Controller
{
    public function index()
    {
        $memories = homeMemories::all();
        return view('dashboard.homePage.memories.index', compact('memories'));
    }

    public function create()
    {
        return view('dashboard.homePage.memories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'nullable',
            'desc'      => 'nullable',
            'quote'     => 'nullable',
            'name'      => 'required',
            'graduated' => 'required',
            'image'     => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('homeMemories', 'public');
        }

        homeMemories::create($data);

        return redirect()
            ->route('memories.index')
            ->with('success', 'Memory added successfully');
    }

    public function edit($id)
    {
        $memory = homeMemories::findOrFail($id);
        return view('dashboard.homePage.memories.update', compact('memory'));
    }

    public function update(Request $request, $id)
    {
        $memory = homeMemories::findOrFail($id);

        $data = $request->validate([
            'title'     => 'nullable',
            'desc'      => 'nullable',
            'quote'     => 'nullable',
            'name'      => 'required',
            'graduated' => 'required',
            'image'     => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            if ($memory->image) {
                Storage::disk('public')->delete($memory->image);
            }

            $data['image'] = $request->file('image')
                ->store('homeMemories', 'public');
        }

        $memory->update($data);

        return redirect()
            ->route('memories.index')
            ->with('success', 'Memory updated successfully');
    }

    public function destroy($id)
    {
        $memory = homeMemories::findOrFail($id);

        if ($memory->image) {
            Storage::disk('public')->delete($memory->image);
        }

        $memory->delete();

        return back()->with('success', 'Memory deleted');
    }
}
