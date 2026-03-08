<?php

namespace App\Mail;

use App\Models\AlumniForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlumniFormReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AlumniForm $form)
    {
    }

    public function build()
    {
        return $this->subject('New Alumni Form Submitted – ' . $this->form->first_name . ' ' . $this->form->last_name)
            ->view('emails.alumni.form-received', [
                'form' => $this->form,
            ]);
    }
}
