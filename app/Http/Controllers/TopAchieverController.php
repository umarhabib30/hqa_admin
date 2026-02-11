<?php

namespace App\Http\Controllers;

use App\Models\TopAchiever;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TopAchieverController extends Controller
{
    public function index()
    {
        $achievers = TopAchiever::latest()->get();
        return view('dashboard.homePage.topAchievers.index', compact('achievers'));
    }

    public function create()
    {
        return view('dashboard.homePage.topAchievers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'title' => 'required|string|max:255',
            // 'desc' => 'nullable|string',
            'image' => 'nullable|image',
            'class_achiever' => 'required|string',
            'achiever_name' => 'required|string',
            'achiever_desc' => 'nullable|string',
            'meta_titles.*' => 'nullable|string|max:255',
            'meta_images.*' => 'nullable|image',
        ]);

        $data = $request->only([
            // 'title',
            // 'desc',
            'class_achiever',
            'achiever_name',
            'achiever_desc'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('topAchievers', 'public');
        }

        $metaData = [];
        $titles = $request->input('meta_titles', []);
        $images = $request->file('meta_images', []);

        foreach ($titles as $index => $title) {
            $title = trim($title);
            if ($title === '')
                continue;

            $item = ['title' => $title, 'image' => null];

            if (isset($images[$index]) && $images[$index]->isValid()) {
                $item['image'] = $images[$index]->store('topAchievers/meta', 'public');
            }

            $metaData[] = $item;
        }

        $data['meta_data'] = $metaData;

        TopAchiever::create($data);

        return redirect()->route('topAchievers.index')->with('success', 'Top Achiever added successfully');
    }

    public function edit($id)
    {
        $achiever = TopAchiever::findOrFail($id);
        return view('dashboard.homePage.topAchievers.update', compact('achiever'));
    }

    public function update(Request $request, $id)
    {
        $achiever = TopAchiever::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image',
            'class_achiever' => 'required|string',
            'achiever_name' => 'required|string',
            'achiever_desc' => 'nullable|string',
            'meta_titles.*' => 'nullable|string|max:255',
            'meta_images.*' => 'nullable|image',
        ]);

        $data = $request->only([
            'class_achiever',
            'achiever_name',
            'achiever_desc'
        ]);

        if ($request->hasFile('image')) {
            if ($achiever->image) {
                Storage::disk('public')->delete($achiever->image);
            }
            $data['image'] = $request->file('image')->store('topAchievers', 'public');
        }

        $metaData = [];
        $titles = $request->input('meta_titles', []);
        $images = $request->file('meta_images', []);
        $existingImages = $request->input('meta_existing_images', []);

        foreach ($titles as $index => $title) {
            $title = trim($title);
            if ($title === '')
                continue;

            $item = ['title' => $title, 'image' => null];

            if (isset($images[$index]) && $images[$index]->isValid()) {
                $item['image'] = $images[$index]->store('topAchievers/meta', 'public');
            } else {
                $existing = $existingImages[$index] ?? '';
                if ($existing !== '') {
                    $item['image'] = $existing;
                }
            }

            $metaData[] = $item;
        }

        $data['meta_data'] = $metaData;

        $achiever->update($data);

        return redirect()->route('topAchievers.index')->with('success', 'Top Achiever updated successfully');
    }

    public function destroy($id)
    {
        $achiever = TopAchiever::findOrFail($id);

        if ($achiever->image) {
            Storage::disk('public')->delete($achiever->image);
        }

        if ($achiever->meta_data) {
            foreach ($achiever->meta_data as $meta) {
                if (!empty($meta['image'])) {
                    Storage::disk('public')->delete($meta['image']);
                }
            }
        }

        $achiever->delete();

        return back()->with('success', 'Deleted successfully');
    }
}