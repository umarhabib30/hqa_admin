@php
    $name = ($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '');
@endphp

<p>Hello {{ trim($name) ?: 'Guest' }},</p>

<p>Thank you for your booking for <strong>{{ $event['event_title'] ?? 'our event' }}</strong>.</p>

<p>We've attached your PDF ticket with a QR code for entry. Please bring it on the event day.</p>

<p><strong>Summary</strong></p>
<ul>
    <li>Payment ID: {{ $paymentIntentId }}</li>
    <li>Booking Type: {{ ucfirst($booking['type'] ?? 'seats') }}</li>
    <li>Paid Amount: ${{ number_format($paidAmount, 2) }}</li>
    <li>Tables: {{ implode(', ', $booking['tables'] ?? []) }}</li>
    <li>Total Seats: {{ $booking['total_seats'] ?? 0 }}</li>
</ul>

<p>If you have any questions, reply to this email.</p>

<p>See you there!</p>
