<?php

namespace App\Mail;

use App\Models\SponserPackageSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SponsorSubscriberCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SponserPackageSubscriber $subscriber)
    {
    }

    public function build()
    {
        $packageTitle = $this->subscriber->package?->title ?? ($this->subscriber->sponsor_type ?? 'Sponsor Package');

        $imageDataUri = null;
        if (!empty($this->subscriber->image) && Storage::disk('public')->exists($this->subscriber->image)) {
            try {
                $binary = Storage::disk('public')->get($this->subscriber->image);
                $mime = File::mimeType(Storage::disk('public')->path($this->subscriber->image)) ?: 'image/jpeg';
                $imageDataUri = 'data:' . $mime . ';base64,' . base64_encode($binary);
            } catch (\Throwable) {
                $imageDataUri = null;
            }
        }

        return $this->subject('New Sponsor Package Subscriber: ' . $packageTitle)
            ->view('emails.sponsor_subscriber_created', [
                'subscriber' => $this->subscriber,
                'imageDataUri' => $imageDataUri,
            ]);
    }
}

