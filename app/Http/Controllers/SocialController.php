<?php

namespace App\Http\Controllers;

use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocialController extends Controller
{
    public function index()
    {
        $socials = Social::latest()->get(); // Just latest added on top
        return view('dashboard.homePage.socials.index', compact('socials'));
    }

    public function create()
    {
        return view('dashboard.homePage.socials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'image' => 'required|image',
            'fblink' => 'nullable|url',
            'ytlink' => 'nullable|url',
            'instalink' => 'nullable|url',
            'tiktoklink' => 'nullable|url',
        ]);

        $data = $request->only(['title', 'desc', 'fblink', 'ytlink', 'tiktoklink', 'instalink']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('socials/icons', 'public');
        }

        Social::create($data);

        return redirect()->route('socials.index')
            ->with('success', 'Social link added successfully');
    }

    public function edit($id)
    {
        $social = Social::findOrFail($id);
        return view('dashboard.homePage.socials.update', compact('social'));
    }

    public function update(Request $request, $id)
    {
        $social = Social::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'image' => 'nullable|image',
            'link' => 'nullable|url',
            'fblink' => 'nullable|url',
            'ytlink' => 'nullable|url',
            'instalink' => 'nullable|url',
            'tiktoklink' => 'nullable|url',
        ]);

        $data = $request->only(['title', 'desc', 'fblink', 'ytlink', 'tiktoklink', 'instalink']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($social->image) {
                Storage::disk('public')->delete($social->image);
            }
            $data['image'] = $request->file('image')->store('socials/icons', 'public');
        }

        $social->update($data);

        return redirect()->route('socials.index')
            ->with('success', 'Social link updated successfully');
    }

    public function destroy($id)
    {
        $social = Social::findOrFail($id);

        if ($social->image) {
            Storage::disk('public')->delete($social->image);
        }

        $social->delete();

        return back()->with('success', 'Social link deleted successfully');
    }
}
