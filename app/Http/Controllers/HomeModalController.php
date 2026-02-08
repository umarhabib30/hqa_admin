<?php

namespace App\Http\Controllers;

use App\Models\HomeModal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeModalController extends Controller
{
    public function index()
    {
        $modals = HomeModal::latest()->get();
        return view('dashboard.homePage.modal.index', compact('modals'));
    }

    public function create()
    {
        return view('dashboard.homePage.modal.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'cdesc'        => 'nullable|string',
            // Added max:2048 (2MB limit)
            'image'        => 'nullable',
            'btn_text'     => 'nullable|string|max:50',
            'btn_link'     => 'nullable|url',
            'general_link' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('home/modal', 'public');
        }

        HomeModal::create($data);

        return redirect()->route('homeModal.index')
            ->with('success', 'New modal added successfully.');
    }

    public function edit(HomeModal $homeModal)
    {
        // Changed variable name to 'homeModal' to match the 'update.blade.php'
        return view('dashboard.homePage.modal.update', ['homeModal' => $homeModal]);
    }

    public function update(Request $request, HomeModal $homeModal)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'cdesc'        => 'nullable|string',
            // Added max:2048 (2MB limit)
            'image'        => 'nullable',
            'btn_text'     => 'nullable|string|max:50',
            'btn_link'     => 'nullable|url',
            'general_link' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            if ($homeModal->image) {
                Storage::disk('public')->delete($homeModal->image);
            }
            $data['image'] = $request->file('image')->store('home/modal', 'public');
        }

        $homeModal->update($data);

        return redirect()->route('homeModal.index')
            ->with('success', 'Modal updated successfully.');
    }

    public function destroy(HomeModal $homeModal)
    {
        if ($homeModal->image) {
            Storage::disk('public')->delete($homeModal->image);
        }

        $homeModal->delete();

        return back()->with('success', 'Modal record and associated image deleted.');
    }
}
