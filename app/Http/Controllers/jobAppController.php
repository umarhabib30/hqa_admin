<?php

namespace App\Http\Controllers;

use App\Models\jobApp as ModelsJobApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobAppController extends Controller
{
    public function index()
    {
        $applications = ModelsJobApp::latest()->get();
        return view('dashboard.career.jobApp.index', compact('applications'));
    }

    public function create()
    {
        return view('dashboard.career.jobApp.create');
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email',
        'phone'      => 'required|string|max:30',
        'years_experience' => 'required|integer|min:0',
        'cv' => 'required',
        'description' => 'nullable|string',
    ]);

    // ✅ Upload CV
    $data['cv_path'] = $request->file('cv')->store('cvs', 'public');

    // ❌ REMOVE TEMP FIELD (IMPORTANT)
    unset($data['cv']);

    ModelsJobApp::create($data);

    return redirect()->route('jobApp.index')
        ->with('success', 'Application submitted successfully');
}


    public function edit(ModelsJobApp $jobApp)
    {
        return view('dashboard.career.jobApp.update', [
            'jobApplication' => $jobApp
        ]);
    }

    public function update(Request $request, ModelsJobApp $jobApp)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'phone'      => 'required|string|max:30',
            'years_experience' => 'required|integer|min:0',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('cv')) {
            Storage::disk('public')->delete($jobApp->cv_path);
            $data['cv_path'] = $request->file('cv')->store('cvs', 'public');
        }

        $jobApp->update($data);

        return redirect()->route('jobApp.index')
            ->with('success', 'Application updated successfully');
    }

    public function destroy(ModelsJobApp $jobApp)
    {
        Storage::disk('public')->delete($jobApp->cv_path);
        $jobApp->delete();

        return back()->with('success', 'Application deleted');
    }
}
