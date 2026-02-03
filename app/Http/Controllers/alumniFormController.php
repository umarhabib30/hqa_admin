<?php

namespace App\Http\Controllers;

use App\Models\AlumniForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class alumniFormController extends Controller
{
    public function index()
    {
        $forms = AlumniForm::latest()->get();
        return view('dashboard.alumni.quranForm.index', compact('forms'));
    }

    public function create()
    {
        return view('dashboard.alumni.quranForm.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'graduation_year' => 'required',
            'status' => 'required',
            'email' => 'required|email|unique:alumni_forms,email',
            'phone' => 'required',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            'college' => 'required',
            'degree' => 'required',
            'company' => 'nullable',
            'job_title' => 'nullable',
            'achievements' => 'nullable',
            'image' => 'nullable|image',
            'document' => 'nullable|file',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('alumni/images', 'public');
        }

        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document')->store('alumni/documents', 'public');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        AlumniForm::create($data);

        return redirect()->route('alumniForm.index')
            ->with('success', 'Form submitted successfully');
    }

    public function edit($id)
    {
        $form = AlumniForm::findOrFail($id);
        return view('dashboard.alumni.quranForm.update', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = AlumniForm::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'graduation_year' => 'required',
            'status' => 'required',
            'email' => 'required|email|unique:alumni_forms,email,' . $form->id,
            'phone' => 'required',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            'college' => 'required',
            'degree' => 'required',
            'company' => 'nullable',
            'job_title' => 'nullable',
            'achievements' => 'nullable',
            'image' => 'nullable|image',
            'document' => 'nullable|file',
        ]);

        if ($request->hasFile('image')) {
            if ($form->image) Storage::disk('public')->delete($form->image);
            $data['image'] = $request->file('image')->store('alumni/images', 'public');
        }

        if ($request->hasFile('document')) {
            if ($form->document) Storage::disk('public')->delete($form->document);
            $data['document'] = $request->file('document')->store('alumni/documents', 'public');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $form->update($data);

        return redirect()->route('alumniForm.index')
            ->with('success', 'Form updated successfully');
    }

    public function destroy($id)
    {
        $form = AlumniForm::findOrFail($id);

        if ($form->image) Storage::disk('public')->delete($form->image);
        if ($form->document) Storage::disk('public')->delete($form->document);

        $form->delete();

        return back()->with('success', 'Form deleted');
    }
    public function show($id)
    {
        $form = AlumniForm::findOrFail($id); // fetch the alumni form
        return view('dashboard.alumni.quranForm.details', compact('form'));
    }
}
