<?php

namespace App\Http\Controllers;

use App\Models\AlumniPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniPostController extends Controller
{
    public function index()
    {
        $posts = AlumniPost::latest()->get();
        return view('dashboard.alumni.recentposts.index', compact('posts'));
    }

    public function create()
    {
        return view('dashboard.alumni.recentposts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'post_date' => 'required|date',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('alumni/posts', 'public');
        }

        AlumniPost::create($data);

        return redirect()
            ->route('alumniPosts.index')
            ->with('success', 'Post created successfully');
    }

    public function edit($id)
    {
        $post = AlumniPost::findOrFail($id);
        return view('dashboard.alumni.recentposts.update', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = AlumniPost::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'post_date' => 'required|date',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $data['image'] = $request->file('image')
                ->store('alumni/posts', 'public');
        }

        $post->update($data);

        return redirect()
            ->route('alumniPosts.index')
            ->with('success', 'Post updated successfully');
    }

    public function destroy($id)
    {
        $post = AlumniPost::findOrFail($id);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return back()->with('success', 'Post deleted');
    }
}
