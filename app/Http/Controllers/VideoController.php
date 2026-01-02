<?php

namespace App\Http\Controllers;

use App\Models\Viedo as video; // Fixed: Viedo → Video
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->get();
        return view('dashboard.homePage.videoSection.index', compact('videos')); // Fixed path
    }

    public function create()
    {
        return view('dashboard.homePage.videoSection.create'); // Fixed path
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'image' => 'required|image',
            'video_link' => 'required|url',
        ]);

        $data = $request->only(['title', 'desc', 'video_link']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('videos/thumbnails', 'public');
        }

        Video::create($data);

        return redirect()->route('videos.index')->with('success', 'Video added successfully');
    }

    public function show($id)
    {
        // Simple redirect to edit page (since you don't want a separate show page)
        return redirect()->route('videos.update', $id);
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        return view('dashboard.homePage.videoSection.update', compact('video')); // Fixed: update → edit
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'image' => 'nullable|image',
            'video_link' => 'required|url',
        ]);

        $data = $request->only(['title', 'desc', 'video_link']);

        if ($request->hasFile('image')) {
            // Delete old thumbnail
            if ($video->image) {
                Storage::disk('public')->delete($video->image);
            }
            $data['image'] = $request->file('image')->store('videos/thumbnails', 'public');
        }

        $video->update($data);

        return redirect()->route('videos.index')->with('success', 'Video updated successfully');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        if ($video->image) {
            Storage::disk('public')->delete($video->image);
        }

        $video->delete();

        return back()->with('success', 'Video deleted successfully');
    }
}