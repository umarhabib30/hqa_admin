<?php

namespace App\Mail;

use App\Models\jobApp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public jobApp $jobApplication)
    {
    }

    public function build()
    {
        return $this->subject('New Teacher Job Application – ' . $this->jobApplication->first_name . ' ' . $this->jobApplication->last_name)
            ->view('emails.career.job-application-received', [
                'application' => $this->jobApplication,
            ]);
    }
}
