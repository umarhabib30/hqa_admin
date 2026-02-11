<?php

namespace App\Mail;

use App\Models\jobApp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public jobApp $jobApplication)
    {
    }

    public function build()
    {
        return $this->subject('Teacher Job Application Received – ' . config('app.name'))
            ->view('emails.career.job-application-confirmation', [
                'application' => $this->jobApplication,
            ]);
    }
}
