<?php

namespace App\Mail;

use App\Models\PtoEventAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PtoEventAttendeeConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PtoEventAttendee $attendee)
    {
        $this->attendee->load('event');
    }

    public function build()
    {
        $event = $this->attendee->event;
        $eventTitle = $event ? $event->title : 'PTO Event';

        return $this->subject('Registration confirmed – ' . $eventTitle)
            ->view('emails.pto.event-registration-confirmation', [
                'attendee' => $this->attendee,
                'event' => $event,
            ]);
    }
}
