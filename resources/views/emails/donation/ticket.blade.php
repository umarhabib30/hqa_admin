@php
    $name = ($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '');
@endphp

<p>Assalamu alaikum {{ trim($name) ?: 'Guest' }},</p>

<p>
    Thank you for registering for the <strong>Houston Quran Academy Annual Ramadan Fundraiser</strong>.
    We are grateful for your commitment to our school and your support of this meaningful Iftar and fundraiser.
    We are excited to welcome you and would be honored to have you join us for Iftar, insha’Allah.
</p>

<p>
    <strong>Event:</strong> February 21st, 4:30 PM – 9:30 PM<br>
    <strong>Venue:</strong> 945 Gessner Rd, Houston, TX 77024 (4th floor, Azalea Ballroom)<br>
    <strong>Parking:</strong> Complimentary self-parking available; valet offered at discounted rates by the Westin.
</p>

<p>
    We've attached your PDF ticket with QR code for entry. Please bring it on the event day.
    If you need any assistance before the event, contact us at <strong>832-762-9212</strong>.
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
    education.
    We look forward to sharing a meaningful and spiritually uplifting evening with you.
</p>

<p>HQA Event Organizing Team</p>
