<?php

namespace App\Http\Controllers;

use App\Models\PtoSubscribeMails;
use Illuminate\Http\Request;

class PtoSubscribeMailController extends Controller
{
    public function index()
    {
        $emails = PtoSubscribeMails::all();
        return view('dashboard.pto.ptoSubscribemails.index', compact('emails'));
    }

    public function create()
    {
        return view('dashboard.pto.ptoSubscribemails.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:pto_subscribe_mails,email',
        ]);

        PtoSubscribeMails::create([
            'email' => $request->email,
        ]);

        return redirect()
            ->route('ptoSubscribemails.index')
            ->with('success', 'Email subscribed successfully');
    }

    public function edit($id)
    {
        $email = PtoSubscribeMails::findOrFail($id);
        return view('dashboard.pto.ptoSubscribemails.update', compact('email'));
    }

    public function update(Request $request, $id)
    {
        $email = PtoSubscribeMails::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:pto_subscribe_mails,email,' . $email->id,
        ]);

        $email->update([
            'email' => $request->email,
        ]);

        return redirect()
            ->route('ptoSubscribemails.index')
            ->with('success', 'Email updated successfully');
    }

    public function destroy($id)
    {
        PtoSubscribeMails::findOrFail($id)->delete();

        return back()->with('success', 'Email deleted');
    }
}
