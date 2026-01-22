<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralDonation;
use App\Models\FundRaisa;

class DonationAdminController extends Controller
{
    public function index()
    {
        // Fetch all donations, newest first
        $donations = GeneralDonation::with('goal')->latest()->get();

        // Path matches: resources/views/dashboard/gernalDonation/donations_index.blade.php
        return view('dashboard.gernalDonation.donations_index', compact('donations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'amount' => 'required|numeric|min:0.01',
            'donation_mode' => 'required|in:paid_now,pledged',
            'payment_id' => 'nullable|string|max:255|unique:general_donations,payment_id',
        ]);

        $latestGoalId = FundRaisa::latest('id')->value('id');

        GeneralDonation::create([
            'fund_raisa_id' => $latestGoalId,
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'amount' => $validated['amount'],
            'donation_mode' => $validated['donation_mode'],
            'payment_id' => $validated['payment_id'] ?? null,
        ]);

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation added successfully.');
    }

    public function edit(GeneralDonation $donation)
    {
        return view('dashboard.gernalDonation.donations_edit', compact('donation'));
    }

    public function update(Request $request, GeneralDonation $donation)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'amount' => 'required|numeric|min:0.01',
            'donation_mode' => 'required|in:paid_now,pledged',
            'payment_id' => 'nullable|string|max:255|unique:general_donations,payment_id,' . $donation->id,
        ]);

        $donation->update([
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'amount' => $validated['amount'],
            'donation_mode' => $validated['donation_mode'],
            'payment_id' => $validated['payment_id'] ?? null,
        ]);

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation updated successfully.');
    }

    public function destroy(GeneralDonation $donation)
    {
        $donation->delete();

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }
}
