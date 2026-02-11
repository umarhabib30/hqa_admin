<?php

namespace App\Mail;

use App\Models\GeneralDonation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralDonationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GeneralDonation $donation)
    {
    }

    public function build()
    {
        $isRecurring = $this->donation->frequency !== 'one_time';
        $subject = $isRecurring
            ? 'Thank you – your recurring donation is set up'
            : 'Thank you for your one-time donation';

        return $this->subject($subject . ' – ' . config('app.name'))
            ->view('emails.donation.general-confirmation', [
                'donation' => $this->donation,
            ]);
    }
}
