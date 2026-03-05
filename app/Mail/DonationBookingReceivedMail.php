<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationBookingReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<string, mixed> $event
     * @param array<string, mixed> $bookingSummary
     */
    public function __construct(
        public array $event,
        public array $bookingSummary,
        public string $paymentId,
        public float $paidAmount
    ) {
    }

    public function build()
    {
        return $this->subject('New Donation Booking – ' . ($this->event['event_title'] ?? 'Event'))
            ->view('emails.donation.booking-received', [
                'event' => $this->event,
                'booking' => $this->bookingSummary,
                'paymentId' => $this->paymentId,
                'paidAmount' => $this->paidAmount,
            ]);
    }
}
