<?php

namespace App\Http\Controllers;

use App\Models\PtoLetterGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PtoLetterGuideController extends Controller
{
    public function index()
    {
        $items = PtoLetterGuide::all();
        return view('dashboard.pto.letter.index', compact('items'));
    }

    public function create()
    {
        return view('dashboard.pto.letter.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'newsletter_download' => 'required|file|mimes:pdf,doc,docx',
            'guide_download' => 'required|file|mimes:pdf,doc,docx',
        ]);

        $newsletterPath = $request->file('newsletter_download')
            ->store('pto/newsletters', 'public');

        $guidePath = $request->file('guide_download')
            ->store('pto/guides', 'public');

        PtoLetterGuide::create([
            'newsletter_download' => $newsletterPath,
            'guide_download' => $guidePath,
        ]);

        return redirect()->route('ptoLetterGuide.index')
            ->with('success', 'PTO Letter Guide created successfully');
    }

    public function edit(string $id)
    {
        $item = PtoLetterGuide::findOrFail($id);
        return view('dashboard.pto.letter.udpate', compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $item = PtoLetterGuide::findOrFail($id);

        if ($request->hasFile('newsletter_download')) {
            Storage::disk('public')->delete($item->newsletter_download);
            $item->newsletter_download = $request->file('newsletter_download')
                ->store('pto/newsletters', 'public');
        }

        if ($request->hasFile('guide_download')) {
            Storage::disk('public')->delete($item->guide_download);
            $item->guide_download = $request->file('guide_download')
                ->store('pto/guides', 'public');
        }

        $item->save();

        return redirect()->route('ptoLetterGuide.index')
            ->with('success', 'PTO Letter Guide updated successfully');
    }

    public function destroy(string $id)
    {
        $item = PtoLetterGuide::findOrFail($id);

        Storage::disk('public')->delete([
            $item->newsletter_download,
            $item->guide_download
        ]);

        $item->delete();

        return redirect()->route('ptoLetterGuide.index')
            ->with('success', 'PTO Letter Guide deleted successfully');
    }
}

