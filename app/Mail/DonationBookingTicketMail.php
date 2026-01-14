<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Dompdf\Dompdf;
use Dompdf\Options;

class DonationBookingTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $event;
    public array $booking;
    public string $paymentIntentId;
    public string $qrCodeDataUrl;
    public float $paidAmount;

    public function __construct(array $event, array $booking, string $paymentIntentId, string $qrCodeDataUrl, float $paidAmount)
    {
        $this->event = $event;
        $this->booking = $booking;
        $this->paymentIntentId = $paymentIntentId;
        $this->qrCodeDataUrl = $qrCodeDataUrl;
        $this->paidAmount = $paidAmount;
    }

    public function build()
    {
        $html = view('emails.donation.ticket-pdf', [
            'event' => $this->event,
            'booking' => $this->booking,
            'paymentIntentId' => $this->paymentIntentId,
            'qrCodeDataUrl' => $this->qrCodeDataUrl,
            'paidAmount' => $this->paidAmount,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        return $this->subject('Your Donation Booking Ticket')
            ->view('emails.donation.ticket')
            ->with([
                'event' => $this->event,
                'booking' => $this->booking,
                'paymentIntentId' => $this->paymentIntentId,
                'paidAmount' => $this->paidAmount,
            ])
            ->attachData($pdfOutput, 'donation-ticket.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
