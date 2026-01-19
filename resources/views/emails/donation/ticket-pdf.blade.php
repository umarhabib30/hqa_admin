<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HQA Fundraiser Ticket - Premium Edition</title>
    <style>
        /**
         * NOTE:
         * Dompdf does NOT execute JavaScript, so Tailwind CDN ("cdn.tailwindcss.com") won't work in PDFs.
         * Keep this template PDF-friendly with plain CSS + table layout.
         */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 24px;
            color: #0f172a;
        }

        .card {
            width: 100%;
            max-width: 920px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            overflow: hidden;
        }

        .header {
            padding: 28px 32px;
        }

        .title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
            margin: 0;
        }

        .subtitle {
            margin-top: 6px;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
        }

        .ticketNo {
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 700;
            margin-top: 8px;
            text-align: right;
        }

        .badge {
            display: inline-block;
            background: #4f46e5;
            width: 56px;
            height: 56px;
            border-radius: 14px;
            text-align: center;
            line-height: 56px;
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
        }

        .hero {
            padding: 0 32px 18px 32px;
        }

        .heroBox {
            height: 200px;
            border-radius: 22px;
            overflow: hidden;
            background: #0f172a;
        }

        .heroBox img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .main {
            width: 100%;
            padding: 22px 32px 32px 32px;
        }

        .leftCol {
            vertical-align: top;
            padding-right: 18px;
        }

        .rightCol {
            vertical-align: top;
            padding-left: 18px;
        }

        .qrBox {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 10px;
            display: inline-block;
            background: #ffffff;
        }

        .info {
            margin-top: 18px;
        }

        .infoRow {
            margin-bottom: 12px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #f8fafc;
        }

        .infoLabel {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #94a3b8;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .infoValue {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
        }

        .details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 20px 20px 18px 20px;
        }

        .kicker {
            font-size: 10px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .type {
            font-size: 24px;
            font-weight: 900;
            font-style: italic;
            margin-bottom: 8px;
        }

        .meta {
            font-size: 12px;
            color: #475569;
            margin-bottom: 16px;
        }

        .sectionLabel {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .attendeeName {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .attendeeEmail {
            font-size: 12px;
            color: #64748b;
            font-style: italic;
            margin-bottom: 14px;
        }

        .seatGrid {
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
        }

        .seatCell {
            text-align: center;
            padding: 8px 6px;
        }

        .seatQty {
            font-size: 20px;
            font-weight: 900;
        }

        .seatLbl {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .footer {
            background: #0f172a;
            padding: 18px 26px;
            text-align: center;
            color: #cbd5e1;
        }

        .footerTitle {
            color: #a5b4fc;
            font-weight: 900;
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .footerText {
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
        }
    </style>
</head>

@php
    use Carbon\Carbon;

    $eventTitle = $event['event_title'] ?? 'Fundraiser';

    $rawId = (string) ($paymentIntentId ?? '');
    $ticketNo = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $rawId), -6));
    $ticketNo = $ticketNo !== '' ? $ticketNo : '---';

    $startDate = !empty($event['event_start_date'])
        ? Carbon::parse($event['event_start_date'])->format('F j, Y')
        : 'TBA';
    $endDate = !empty($event['event_end_date'])
        ? Carbon::parse($event['event_end_date'])->format('F j, Y')
        : null;
    $dateLabel = $endDate && $endDate !== $startDate ? ($startDate . ' - ' . $endDate) : $startDate;

    $startTime = !empty($event['event_start_time']) ? Carbon::parse($event['event_start_time'])->format('g:i A') : null;
    $endTime = !empty($event['event_end_time']) ? Carbon::parse($event['event_end_time'])->format('g:i A') : null;
    $timeLabel = $startTime && $endTime ? ($startTime . ' - ' . $endTime) : ($startTime ?: ($endTime ?: 'TBA'));

    $location = $event['event_location'] ?? 'TBA';

    $name = trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? ''));
    $name = $name !== '' ? $name : 'Guest';
    $email = $booking['email'] ?? '';

    $bookingType = (string) ($booking['type'] ?? 'seats');
    $typeLabel = $bookingType === 'full_table' ? 'FULL TABLE' : 'SEATS';

    $seatTypes = is_array($booking['seat_types'] ?? null) ? $booking['seat_types'] : [];
    $totalSeats = (int) ($booking['total_seats'] ?? 0);

    $tables = (array) ($booking['tables'] ?? []);
    $tablesLabel = count($tables) ? implode(', ', $tables) : 'N/A';

    $paidLabel = '$' . number_format((float) ($paidAmount ?? 0), 2);
@endphp

<body>
    <div class="card">
        <div class="header">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="vertical-align: top;">
                        <div class="title">{{ $eventTitle }}</div>
                        <div class="subtitle">Houston Quran Academy</div>
                    </td>
                    <td style="vertical-align: top; text-align: right;">
                        <div class="badge">HQA</div>
                        <div class="ticketNo">Ticket #{{ $ticketNo }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="hero">
            <div class="heroBox">
                <img src="https://images.unsplash.com/photo-1541339907198-e08756ebafe3?q=80&w=2070&auto=format&fit=crop"
                    alt="Event image" />
            </div>
        </div>

        <div class="main">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="leftCol" width="42%">
                        <div class="qrBox">
                            <img src="{{ $qrCodeDataUrl }}" alt="Ticket QR Code" width="160" height="160" />
                        </div>

                        <div class="info">
                            <div class="infoRow">
                                <div class="infoLabel">Date</div>
                                <div class="infoValue">{{ $dateLabel }}</div>
                            </div>
                            <div class="infoRow">
                                <div class="infoLabel">Time</div>
                                <div class="infoValue">{{ $timeLabel }}</div>
                            </div>
                            <div class="infoRow">
                                <div class="infoLabel">Location</div>
                                <div class="infoValue">{!! nl2br(e($location)) !!}</div>
                            </div>
                        </div>
                    </td>

                    <td class="rightCol" width="58%">
                        <div class="details">
                            <div class="kicker">Ticket Type</div>
                            <div class="type">{{ $typeLabel }}</div>
                            <div class="meta">
                                <strong>Tables:</strong> {{ $tablesLabel }}
                                &nbsp;&nbsp;•&nbsp;&nbsp;
                                <strong>Paid:</strong> {{ $paidLabel }}
                            </div>

                            <div class="sectionLabel">Primary Attendee</div>
                            <div class="attendeeName">{{ $name }}</div>
                            <div class="attendeeEmail">{{ $email }}</div>

                            <div class="seatGrid">
                                @if($bookingType === 'full_table')
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="seatCell">
                                                <div class="seatQty">{{ str_pad((string) $totalSeats, 2, '0', STR_PAD_LEFT) }}</div>
                                                <div class="seatLbl">Seats (Full Table)</div>
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    @php
                                        $filteredSeatTypes = [];
                                        foreach ($seatTypes as $t => $q) {
                                            if ((int) $q > 0) $filteredSeatTypes[$t] = (int) $q;
                                        }
                                        $chunks = array_chunk($filteredSeatTypes, 3, true);
                                    @endphp

                                    @if(count($filteredSeatTypes))
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            @foreach($chunks as $row)
                                                <tr>
                                                    @foreach($row as $t => $q)
                                                        <td class="seatCell" width="33%">
                                                            <div class="seatQty">{{ str_pad((string) $q, 2, '0', STR_PAD_LEFT) }}</div>
                                                            <div class="seatLbl">{{ ucwords(str_replace(['_', '-'], ' ', (string) $t)) }}</div>
                                                        </td>
                                                    @endforeach
                                                    @for($i = count($row); $i < 3; $i++)
                                                        <td class="seatCell" width="33%">&nbsp;</td>
                                                    @endfor
                                                </tr>
                                            @endforeach
                                        </table>
                                    @else
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="seatCell">
                                                    <div class="seatQty">{{ str_pad((string) $totalSeats, 2, '0', STR_PAD_LEFT) }}</div>
                                                    <div class="seatLbl">Total Seats</div>
                                                </td>
                                            </tr>
                                        </table>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div class="footerTitle">Thank you for your generous support</div>
            <p class="footerText">
                This ticket is required for entry. Please have the QR code ready for scanning at the reception desk.
                All proceeds benefit the HQA Education Fund.
            </p>
        </div>
    </div>
</body>

</html>
