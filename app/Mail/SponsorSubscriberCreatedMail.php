<?php

namespace App\Mail;

use App\Models\SponserPackageSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SponsorSubscriberCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SponserPackageSubscriber $subscriber)
    {
    }

    public function build()
    {
        $packageTitle = $this->subscriber->package?->title ?? ($this->subscriber->sponsor_type ?? 'Sponsor Package');

        return $this->subject('New Sponsor Package Subscriber: ' . $packageTitle)
            ->view('emails.sponsor_subscriber_created', [
                'subscriber' => $this->subscriber,
            ]);
    }
}

