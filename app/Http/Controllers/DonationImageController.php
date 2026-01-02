<?php

namespace App\Http\Controllers;

use App\Models\DonationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationImageController extends Controller
{
    public function index()
    {
        $galleries = DonationImage::latest()->get();
        return view('dashboard.donation.multipleimage.index', compact('galleries'));
    }

    public function create()
    {
        return view('dashboard.donation.multipleimage.create');
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image',
        ]);

        $paths = [];

        foreach ($request->file('images') as $file) {
            $paths[] = $file->store('donation/gallery', 'public');
        }

        DonationImage::create([
            'images' => $paths, // ✅ array saved in ONE row
        ]);

        return redirect()
            ->route('donationImage.index')
            ->with('success', 'Images uploaded successfully');
    }

    /* ================= EDIT ================= */
    public function edit($id)
    {
        $gallery = DonationImage::findOrFail($id);
        return view('dashboard.donation.multipleimage.update', compact('gallery'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $id)
    {
        $gallery = DonationImage::findOrFail($id);

        $request->validate([
            'images.*' => 'image',
        ]);

        $existingImages = $gallery->images ?? [];

        // remove selected images
        if ($request->remove_images) {
            foreach ($request->remove_images as $img) {
                Storage::disk('public')->delete($img);
                $existingImages = array_values(array_diff($existingImages, [$img]));
            }
        }

        // add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $existingImages[] = $file->store('donation/gallery', 'public');
            }
        }

        $gallery->update([
            'images' => $existingImages,
        ]);

        return redirect()
            ->route('donationImage.index')
            ->with('success', 'Gallery updated successfully');
    }

    /* ================= DELETE (FULL GALLERY) ================= */
    public function destroy($id)
    {
        $gallery = DonationImage::findOrFail($id);

        foreach ($gallery->images as $img) {
            Storage::disk('public')->delete($img);
        }

        $gallery->delete();

        return back()->with('success', 'Gallery deleted');
    }
}
