<?php

namespace App\Http\Controllers;

use App\Models\SponsorPackage;
use Illuminate\Http\Request;

class SponsorPackageController extends Controller
{
    /**
     * Display a listing of the sponsor packages.
     */
    public function index()
    {
        $packages = SponsorPackage::latest()->get();
        return view('dashboard.sponsor-packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new sponsor package.
     */
    public function create()
    {
        return view('dashboard.sponsor-packages.create');
    }

    /**
     * Store a newly created sponsor package in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price_per_year' => 'required|numeric|min:0',
            'benefits' => 'required|array|min:1',
            'benefits.*' => 'required|string|max:500',
        ]);

        SponsorPackage::create([
            'title' => $request->title,
            'price_per_year' => $request->price_per_year,
            'benefits' => array_filter($request->benefits), // Remove empty values
        ]);

        return redirect()
            ->route('sponsor-packages.index')
            ->with('success', 'Sponsor package created successfully.');
    }

    /**
     * Display the specified sponsor package.
     */
    public function show($id)
    {
        $package = SponsorPackage::findOrFail($id);
        return view('dashboard.sponsor-packages.show', compact('package'));
    }

    /**
     * Show the form for editing the specified sponsor package.
     */
    public function edit($id)
    {
        $package = SponsorPackage::findOrFail($id);
        return view('dashboard.sponsor-packages.edit', compact('package'));
    }

    /**
     * Update the specified sponsor package in storage.
     */
    public function update(Request $request, $id)
    {
        $package = SponsorPackage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'price_per_year' => 'required|numeric|min:0',
            'benefits' => 'required|array|min:1',
            'benefits.*' => 'required|string|max:500',
        ]);

        $package->update([
            'title' => $request->title,
            'price_per_year' => $request->price_per_year,
            'benefits' => array_filter($request->benefits), // Remove empty values
        ]);

        return redirect()
            ->route('sponsor-packages.index')
            ->with('success', 'Sponsor package updated successfully.');
    }

    /**
     * Remove the specified sponsor package from storage.
     */
    public function destroy($id)
    {
        $package = SponsorPackage::findOrFail($id);
        $package->delete();

        return redirect()
            ->route('sponsor-packages.index')
            ->with('success', 'Sponsor package deleted successfully.');
    }
}
