<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();
        return view('dashboard.homePage.newsSection.index', compact('news'));
    }

    public function create()
    {
        return view('dashboard.homePage.newsSection.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable|image',
            'video_link' => 'nullable|url',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $data['social_links'] = $request->social_links;

        News::create($data);

        return redirect()->route('news.index')->with('success', 'News created');
    }

    public function edit(News $news)
    {
        return view('dashboard.homePage.newsSection.update', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable|image',
            'video_link' => 'nullable|url',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $data['social_links'] = $request->social_links;

        $news->update($data);

        return redirect()->route('news.index')->with('success', 'News updated');
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return back()->with('success', 'News deleted');
    }
}
