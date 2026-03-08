<?php

namespace App\Http\Controllers;

use App\Models\ContactSponserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSponserMail;
use App\Mail\ContactSponserConfirmationMail;
use App\Services\MailRecipientResolver;
use Illuminate\Support\Facades\Log;

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

        // Confirm to user first so they always get confirmation even if admin mail fails
        if (!empty($contact->email)) {
            try {
                Mail::to($contact->email)->send(new ContactSponserConfirmationMail($contact));
            } catch (\Throwable $e) {
                Log::warning('Contact sponsor: failed to send confirmation to contact', [
                    'contact_id' => $contact->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Notify internal recipients based on Contact Sponsor permission
        $resolver = app(MailRecipientResolver::class);
        $adminEmails = $resolver->resolveByModule('contact_sponsor', static::class . '@store');
        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new ContactSponserMail($contact));
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
