@php
    use Carbon\Carbon;

    // Event details (dynamic; if missing, show N/A to avoid misleading attendees)
    $eventTitle = !empty($event['event_title']) ? $event['event_title'] : 'N/A';

    $eventDate = 'N/A';
    if (!empty($event['event_start_date'])) {
        try {
            $eventDate = Carbon::parse($event['event_start_date'])->format('F jS');
        } catch (\Throwable $e) {
            $eventDate = 'N/A';
        }
    }

    $timeRange = 'N/A';
    if (!empty($event['event_start_time']) && !empty($event['event_end_time'])) {
        try {
            $start = Carbon::parse($event['event_start_time'])->format('g:i A');
            $end = Carbon::parse($event['event_end_time'])->format('g:i A');
            $timeRange = $start . ' to ' . $end;
        } catch (\Throwable $e) {
            $timeRange = 'N/A';
        }
    }

    $venue = !empty($event['event_location']) ? $event['event_location'] : 'N/A';
    $contact = !empty($event['contact_number']) ? $event['contact_number'] : 'N/A';
@endphp

<p>Assalamu alaikum,</p>

<p>
    Thank you for registering for the {{ $eventTitle }}. We are grateful for your commitment to our school and your
    support of this meaningful Iftar and fundraiser. We are excited to welcome you and would be honored to have you
    join us for Iftar, insha’Allah, on {{ $eventDate }} from {{ $timeRange }}.
</p>

<p>
    The event will be held at {{ $venue }}. Complimentary self-parking will be available for our event, and valet
    parking is also offered at discounted rates by the Westin for your convenience. If you need any assistance or have
    questions prior to the event, please feel free to contact us at {{ $contact }}.
</p>

<p>
    We've attached your PDF ticket with QR code for entry. Please bring it on the event day.
</p>

<p><strong>Your Booking</strong></p>
<ul>
    <li>Payment ID: {{ $paymentIntentId }}</li>
    <li>Booking Type: {{ ucfirst($booking['type'] ?? 'seats') }}</li>
    <li>Paid Amount: ${{ number_format($paidAmount, 2) }}</li>
    <li>Total Seats: {{ $booking['total_seats'] ?? 0 }}</li>
</ul>

<p>
    May Allah accept our fasts, our efforts, and our intentions, and bless you for your continued support of Islamic
    education. We look forward to sharing a meaningful and spiritually uplifting evening with you.
</p>

<p>HQA Event Organising Team</p>
