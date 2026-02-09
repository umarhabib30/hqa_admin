<?php

namespace App\Mail;

use App\Models\AlumniEventAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlumniEventAttendeeConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AlumniEventAttendee $attendee)
    {
        $this->attendee->load('event');
    }

    public function build()
    {
        $event = $this->attendee->event;
        $eventTitle = $event ? $event->title : 'Alumni Event';

        return $this->subject('Registration confirmed – ' . $eventTitle)
            ->view('emails.alumni.event-registration-confirmation', [
                'attendee' => $this->attendee,
                'event' => $event,
            ]);
    }
}
