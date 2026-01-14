@php
    $name = ($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Donation Booking Ticket</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .label { color: #6b7280; font-size: 12px; }
        .value { font-size: 14px; font-weight: 600; }
        .qr { text-align: center; margin-top: 16px; }
    </style>
</head>
<body>
    <h2>Donation Booking Ticket</h2>
    <p><strong>{{ $event['event_title'] ?? 'Event' }}</strong></p>
    <p>{{ $event['event_location'] ?? '' }}</p>
    <p>{{ $event['event_start_date'] ?? '' }} {{ $event['event_start_time'] ?? '' }} - {{ $event['event_end_date'] ?? '' }} {{ $event['event_end_time'] ?? '' }}</p>

    <div class="card">
        <div class="row">
            <div class="label">Name</div>
            <div class="value">{{ trim($name) ?: 'Guest' }}</div>
        </div>
        <div class="row">
            <div class="label">Email</div>
            <div class="value">{{ $booking['email'] ?? '' }}</div>
        </div>
        <div class="row">
            <div class="label">Phone</div>
            <div class="value">{{ $booking['phone'] ?? '' }}</div>
        </div>
        <div class="row">
            <div class="label">Booking Type</div>
            <div class="value">{{ ucfirst($booking['type'] ?? 'seats') }}</div>
        </div>
        <div class="row">
            <div class="label">Tables</div>
            <div class="value">{{ implode(', ', $booking['tables'] ?? []) }}</div>
        </div>
        <div class="row">
            <div class="label">Seats</div>
            <div class="value">
                {{ $booking['total_seats'] ?? 0 }}
                @if(!empty($booking['seat_types']))
                    ( @foreach($booking['seat_types'] as $t => $q) {{ $t }}: {{ $q }}@if(!$loop->last), @endif @endforeach )
                @endif
            </div>
        </div>
        <div class="row">
            <div class="label">Payment ID</div>
            <div class="value">{{ $paymentIntentId }}</div>
        </div>
        <div class="row">
            <div class="label">Paid Amount</div>
            <div class="value">${{ number_format($paidAmount, 2) }}</div>
        </div>
    </div>

    <div class="qr">
        <p>Show this QR code at entry</p>
        <img src="{{ $qrCodeDataUrl }}" alt="QR Code" width="240" height="240">
    </div>
</body>
</html>
