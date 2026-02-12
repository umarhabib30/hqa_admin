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
        $goals = FundRaisa::orderByDesc('id')->get();

        return view('dashboard.gernalDonation.donations_index', compact('donations', 'goals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'amount' => 'required|numeric|min:0.01',
            'donation_mode' => 'required|in:paid_now,pledged',
            'payment_id' => 'nullable|string|max:255|unique:general_donations,payment_id',
            'donation_for' => 'required|string|max:255',
            'fund_raisa_id' => 'nullable|integer|exists:fund_raisas,id',

            // ✅ NEW ADDRESS FIELDS
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city'     => 'required|string|max:255',
            'state'    => 'required|string|max:255',
            'country'  => 'required|string|max:255',
        ]);

        $goalId = isset($validated['fund_raisa_id']) ? (int) $validated['fund_raisa_id'] : FundRaisa::latest('id')->value('id');

        GeneralDonation::create([
            'fund_raisa_id' => $goalId,
            'donation_for'  => $validated['donation_for'],
            'name'          => $validated['name'] ?? null,
            'email'         => $validated['email'] ?? null,
            'amount'        => $validated['amount'],
            'donation_mode' => $validated['donation_mode'],
            'payment_id'    => $validated['payment_id'] ?? null,

            // ✅ ADDRESS SAVE
            'address1' => $validated['address1'],
            'address2' => $validated['address2'] ?? null,
            'city'     => $validated['city'],
            'state'    => $validated['state'],
            'country'  => $validated['country'],
        ]);

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation added successfully.');
    }

    public function edit(GeneralDonation $donation)
    {
        $goals = FundRaisa::orderByDesc('id')->get();
        return view('dashboard.gernalDonation.donations_edit', compact('donation', 'goals'));
    }

    public function update(Request $request, GeneralDonation $donation)
    {
        $validated = $request->validate([
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'amount'        => 'required|numeric|min:0',
            'donation_mode' => 'required|in:paid_now,pledged,stripe',
            'payment_id'    => 'nullable|string|max:255|unique:general_donations,payment_id,' . $donation->id,
            'donation_for'  => 'required|string|max:255',
            'fund_raisa_id' => 'nullable|integer|exists:fund_raisas,id',
            'address1'      => 'required|string|max:255',
            'address2'      => 'nullable|string|max:255',
            'city'          => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'country'       => 'required|string|max:255',
            'frequency'     => 'nullable|string|max:50',
            'status'        => 'nullable|string|max:50',
        ]);

        $donation->update([
            'fund_raisa_id'  => $validated['fund_raisa_id'] ?? null,
            'donation_for'   => $validated['donation_for'],
            'name'           => $validated['name'] ?? null,
            'email'          => $validated['email'] ?? null,
            'amount'         => $validated['amount'],
            'donation_mode'  => $validated['donation_mode'],
            'payment_id'     => $validated['payment_id'] ?? null,
            'address1'       => $validated['address1'],
            'address2'       => $validated['address2'] ?? null,
            'city'           => $validated['city'],
            'state'          => $validated['state'],
            'country'        => $validated['country'],
            'frequency'      => $validated['frequency'] ?? $donation->frequency,
            'status'         => $validated['status'] ?? $donation->status,
        ]);

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation updated successfully.');
    }

    public function show(GeneralDonation $donation)
    {
        // Load the relationship to ensure goal name is available
        $donation->load('goal');

        return view('dashboard.gernalDonation.donations_show', compact('donation'));
    }
    public function destroy(GeneralDonation $donation)
    {
        $donation->delete();

        return redirect()
            ->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }
}
