<?php

namespace App\Mail;

use App\Models\AlumniForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlumniFormConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AlumniForm $form)
    {
    }

    public function build()
    {
        return $this->subject('Thank you for submitting your Alumni Form')
            ->view('emails.alumni.form-confirmation', [
                'form' => $this->form,
            ]);
    }
}

