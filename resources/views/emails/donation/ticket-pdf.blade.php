<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Donation Booking Ticket</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #111;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .ticket-box {
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 8px;
        }

        /* Dompdf needs tables for alignment, not Flexbox */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
        }

        .label {
            color: #6b7280;
            font-size: 12px;
            width: 40%;
        }

        .value {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            width: 60%;
        }

        .footer-text {
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
            margin-top: 10px;
        }
        .qr-section {
            text-align: center;
            margin-top: 24px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>HQA Annual Ramadan Fundraiser Ticket</h2>
        <p style="font-size: 13px; margin: 4px 0;">Houston Quran Academy</p>
    </div>

    <div class="ticket-box">
        <table class="info-table" style="margin-bottom: 10px;">
            <tr>
                <td class="label">Event</td>
                <td class="value">Annual Ramadan Fundraiser</td>
            </tr>
            <tr>
                <td class="label">Date & Time</td>
                <td class="value">Feb 21, 4:30 PM – 9:30 PM</td>
            </tr>
            <tr>
                <td class="label">Venue</td>
                <td class="value" style="font-size: 12px; line-height: 1.4;">
                    945 Gessner Rd, Houston, TX 77024<br>
                    4th Floor, Azalea Ballroom
                </td>
            </tr>
            <tr>
                <td class="label">Parking</td>
                <td class="value" style="font-size: 12px;">Complimentary self-parking; valet discounted by Westin.</td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="label">Name</td>
                <td class="value">{{ ($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '') }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="value">{{ $booking['email'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Booking Type</td>
                <td class="value">{{ ucfirst($booking['type'] ?? 'seats') }}</td>
            </tr>
            <tr>
                <td class="label">Tables Assigned</td>
                <td class="value">{{ implode(', ', (array) ($booking['tables'] ?? [])) }}</td>
            </tr>
            <tr>
                <td class="label">Total Seats</td>
                <td class="value">{{ $booking['total_seats'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Paid Amount</td>
                <td class="value">${{ number_format($paidAmount, 2) }}</td>
            </tr>
            <tr>
                <td class="label" style="border:none;">Payment ID</td>
                <td class="value" style="border:none; font-size: 10px;">{{ $paymentIntentId }}</td>
            </tr>
        </table>
    </div>

    @if(!empty($qrCodeDataUrl))
    <div class="qr-section">
        <p style="font-size: 12px; margin-bottom: 8px;">Show this QR code at entry</p>
        <img src="{{ $qrCodeDataUrl }}" width="200" height="200" alt="QR Code">
    </div>
    @endif

    <p class="footer-text">Generated on {{ date('Y-m-d H:i') }}</p>
</body>

</html>
