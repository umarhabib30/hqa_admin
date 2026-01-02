<?php

namespace App\Mail;

use App\Models\AlumniEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAlumniEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;

    public function __construct(AlumniEvent $event)
    {
        $this->event = $event;
    }

    public function build()
    {
        return $this->subject('🎓 New Alumni Event Added – Check it Out')
            ->view('emails.alumni.new-event');
    }
}
