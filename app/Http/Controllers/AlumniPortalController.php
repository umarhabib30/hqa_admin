<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlumniPortalController extends Controller
{
    /**
     * Alumni portal dashboard (after login).
     */
    public function dashboard()
    {
        $alumni = Auth::guard('alumni')->user();

        return view('alumni.dashboard', compact('alumni'));
    }

    /**
     * Show form to edit own profile.
     */
    public function editProfile()
    {
        $alumni = Auth::guard('alumni')->user();

        return view('alumni.profile.edit', compact('alumni'));
    }

    /**
     * Update own profile.
     */
    public function updateProfile(Request $request)
    {
        $alumni = Auth::guard('alumni')->user();

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'graduation_year' => 'required',
            'status' => 'required|in:single,married',
            'email' => 'required|email|unique:alumni_forms,email,' . $alumni->id,
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:255',
            'college' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'achievements' => 'nullable|string',
            'image' => 'nullable|image',
            'document' => 'nullable|file',
        ]);

        if ($request->hasFile('image')) {
            if ($alumni->image) {
                Storage::disk('public')->delete($alumni->image);
            }
            $data['image'] = $request->file('image')->store('alumni/images', 'public');
        }

        if ($request->hasFile('document')) {
            if ($alumni->document) {
                Storage::disk('public')->delete($alumni->document);
            }
            $data['document'] = $request->file('document')->store('alumni/documents', 'public');
        }

        $alumni->update($data);

        return redirect()
            ->route('alumni.dashboard')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password only.
     */
    public function updatePassword(Request $request)
    {
        $alumni = Auth::guard('alumni')->user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $alumni->password = $request->password;
        $alumni->save();

        return redirect()
            ->route('alumni.profile.edit')
            ->with('success', 'Password updated successfully.');
    }
}
