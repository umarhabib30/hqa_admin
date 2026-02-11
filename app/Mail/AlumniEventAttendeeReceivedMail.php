<?php

namespace App\Mail;

use App\Models\AlumniEventAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlumniEventAttendeeReceivedMail extends Mailable
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
        $name = trim(($this->attendee->first_name ?? '') . ' ' . ($this->attendee->last_name ?? '')) ?: $this->attendee->email;

        return $this->subject('New alumni event registration – ' . $name . ' (' . $eventTitle . ')')
            ->view('emails.alumni.event-registration-received', [
                'attendee' => $this->attendee,
                'event' => $event,
            ]);
    }
}
