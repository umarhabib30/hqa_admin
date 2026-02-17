<?php

namespace App\Mail;

use App\Models\GeneralDonation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralDonationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(public GeneralDonation $donation, public array $payload = [])
    {
    }

    public function build()
    {
        $type = $this->donation->frequency === 'one_time' ? 'One-time' : 'Recurring';
        $name = $this->donation->name ?: $this->donation->email;

        return $this->subject('New general donation (' . $type . ') – ' . $name)
            ->view('emails.donation.general-received', [
                'donation' => $this->donation,
                'payload' => $this->payload,
            ]);
    }
}
