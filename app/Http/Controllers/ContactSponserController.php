<?php

namespace App\Http\Controllers;

use App\Models\ContactSponserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSponserMail;
class ContactSponserController extends Controller
{
    public function store(Request $request)
    {
        $contact = ContactSponserModel::create([
            'full_name' => $request->full_name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'sponsor_type' => $request->sponsor_type,
            'message' => $request->message,
        ]);
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new ContactSponserMail($contact));
        } else {
            // Fallback: keep previous behavior if MAIL_ADMIN_EMAIL not set
            Mail::to('mumarhabibrb102@gmail.com')->queue(new ContactSponserMail($contact));
        }
        return response()->json([
            'status' => true,
            'message' => 'Contact created successfully',
            'data' => $contact
        ]);
    }

    public function getContactSponser()
    {
        $contact = ContactSponserModel::all();
        return response()->json([
            'status' => true,
            'message' => 'Contact fetched successfully',
            'data' => $contact
        ]);
    }

    public function index()
    {
        $contacts = ContactSponserModel::latest()->get();  // optional: latest first
        return view('dashboard.contact-sponser.index', compact('contacts'));
    }

    public function show($id){
        $contact = ContactSponserModel::find($id);
        return view('dashboard.contact-sponser.detail', compact('contact'));
    }

    public function destroy($id)
    {
        $contact = ContactSponserModel::findOrFail($id);
        $contact->delete();

        return redirect()
            ->route('contact-sponser.index')
            ->with('success', 'Contact deleted successfully.');
    }
}
