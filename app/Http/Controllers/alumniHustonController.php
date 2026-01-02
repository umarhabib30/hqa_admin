<?php

namespace App\Http\Controllers;

use App\Models\AlumniHuston;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniHustonController extends Controller
{
    public function index()
    {
        $alumni = AlumniHuston::all();
        return view('dashboard.alumni.Huston.index', compact('alumni'));
    }

    public function create()
    {
        return view('dashboard.alumni.Huston.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('alumni', 'public');
        }

        AlumniHuston::create($data);

        return redirect()
            ->route('alumniHuston.index')
            ->with('success', 'Alumni added successfully');
    }

    public function edit($id)
    {
        $alumni = AlumniHuston::findOrFail($id);
        return view('dashboard.alumni.Huston.update', compact('alumni'));
    }

    public function update(Request $request, $id)
    {
        $alumni = AlumniHuston::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($alumni->image) {
                Storage::disk('public')->delete($alumni->image);
            }

            $data['image'] = $request->file('image')
                ->store('alumni', 'public');
        }

        $alumni->update($data);

        return redirect()
            ->route('alumniHuston.index')
            ->with('success', 'Alumni updated successfully');
    }

    public function destroy($id)
    {
        $alumni = AlumniHuston::findOrFail($id);

        if ($alumni->image) {
            Storage::disk('public')->delete($alumni->image);
        }

        $alumni->delete();

        return back()->with('success', 'Alumni deleted');
    }
}
