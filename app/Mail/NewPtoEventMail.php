<?php

namespace App\Mail;

use App\Models\PtoEvents;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPtoEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;

    public function __construct(PtoEvents $event)
    {
        $this->event = $event;
    }

    public function build()
    {
        return $this->subject('📢 New PTO Event Added – Check it Out')
            ->view('emails.pto.new-event');
    }
}
