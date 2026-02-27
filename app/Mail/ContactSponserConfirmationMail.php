<?php

namespace App\Mail;

use App\Models\ContactSponserModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactSponserConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactSponserModel $contact)
    {
    }

    public function build()
    {
        return $this->subject('Thank you for contacting us')
            ->view('emails.contact-sponser-confirmation', [
                'contact' => $this->contact,
            ]);
    }
}

