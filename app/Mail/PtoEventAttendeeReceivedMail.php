<?php

namespace App\Mail;

use App\Models\PtoEventAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PtoEventAttendeeReceivedMail extends Mailable
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
        $name = trim(($this->attendee->first_name ?? '') . ' ' . ($this->attendee->last_name ?? '')) ?: $this->attendee->email;

        return $this->subject('New PTO event registration – ' . $name . ' (' . $eventTitle . ')')
            ->view('emails.pto.event-registration-received', [
                'attendee' => $this->attendee,
                'event' => $event,
            ]);
    }
}
