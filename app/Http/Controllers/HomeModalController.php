<?php

namespace App\Http\Controllers;

use App\Models\homeModal;
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
            'title' => 'required',
            'cdesc' => 'nullable',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('home/modal', 'public');
        }

        HomeModal::create($data);

        return redirect()->route('homeModal.index')
            ->with('success', 'Modal created successfully');
    }

    public function edit($id)
    {
        $modal = HomeModal::findOrFail($id);
        return view('dashboard.homePage.modal.update', compact('modal'));
    }

    public function update(Request $request, $id)
    {
        $modal = HomeModal::findOrFail($id);

        $data = $request->validate([
            'title' => 'required',
            'cdesc' => 'nullable',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            if ($modal->image) {
                Storage::disk('public')->delete($modal->image);
            }
            $data['image'] = $request->file('image')->store('home/modal', 'public');
        }

        $modal->update($data);

        return redirect()->route('homeModal.index')
            ->with('success', 'Modal updated successfully');
    }

    public function destroy($id)
    {
        $modal = HomeModal::findOrFail($id);

        if ($modal->image) {
            Storage::disk('public')->delete($modal->image);
        }

        $modal->delete();
        return back()->with('success', 'Modal deleted');
    }
}
