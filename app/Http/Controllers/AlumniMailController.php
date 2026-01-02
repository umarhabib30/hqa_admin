<?php

namespace App\Http\Controllers;

use App\Models\AlumniMail;
use Illuminate\Http\Request;

class AlumniMailController extends Controller
{
    public function index()
    {
        $emails = AlumniMail::all();
        return view('dashboard.alumni.alumniMail.index', compact('emails'));
    }

    public function create()
    {
        return view('dashboard.alumni.alumniMail.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        AlumniMail::create([
            'email' => $request->email,
        ]);

        return redirect()
            ->route('alumniMail.index')
            ->with('success', 'Email subscribed successfully');
    }

    public function edit($id)
    {
        $email = AlumniMail::findOrFail($id);
        return view('dashboard.alumni.alumniMail.update', compact('email'));
    }

    public function update(Request $request, $id)
    {
        $email = AlumniMail::findOrFail($id);

        $request->validate([
            'email' => 'required|email' ,
        ]);

        $email->update([
            'email' => $request->email,
        ]);

        return redirect()
            ->route('alumniMail.index')
            ->with('success', 'Email updated successfully');
    }

    public function destroy($id)
    {
        AlumniMail::findOrFail($id)->delete();

        return back()->with('success', 'Email deleted');
    }
}
