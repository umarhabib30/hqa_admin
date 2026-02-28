<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Donation Booking Report</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 18px;
            font-size: 12px;
            color: #0f172a;
            line-height: 1.35;
            background: #fff;
        }

        .card {
            border: 1px solid #dbeafe;
            background: #f8fbff;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            color: #0b1f44;
        }

        .muted {
            color: #475569;
            font-size: 12px;
            margin-top: 4px;
        }

        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 14px;
        }

        .summary td {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            text-align: center;
            padding: 10px;
        }

        .summary .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .summary .value {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .table-wrap {
            border: 1px solid #dbe3f0;
            border-radius: 10px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #0b1f44;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 8px;
            text-align: left;
        }

        tbody td {
            padding: 8px;
            border-top: 1px solid #e2e8f0;
            vertical-align: top;
            word-break: break-word;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .small {
            font-size: 10px;
            color: #64748b;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Donation Booking Report</h1>
        <div class="muted"><strong>Event:</strong> {{ $event->event_title }}</div>
        <div class="muted">
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_start_date)->format('M d, Y') }}
            @if (!empty($event->event_end_date) && $event->event_end_date !== $event->event_start_date)
                to {{ \Carbon\Carbon::parse($event->event_end_date)->format('M d, Y') }}
            @endif
            | <strong>Location:</strong> {{ $event->event_location }}
        </div>

    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Records</div>
                <div class="value">{{ count($rows) }}</div>
            </td>
            <td>
                <div class="label">Total Seats</div>
                <div class="value">{{ collect($rows)->sum('seats') }}</div>
            </td>
            <td>
                <div class="label">Total Baby Sitting</div>
                <div class="value">{{ collect($rows)->sum('baby_sitting') }}</div>
            </td>
            <td>
                <div class="label">Total Paid</div>
                <div class="value">${{ number_format((float) collect($rows)->sum('amount'), 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">Table</th>
                    <th style="width: 14%;">Name</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 9%;">Phone</th>
                    <th style="width: 8%;">Type</th>
                    <th style="width: 6%;">Seats</th>
                    <th style="width: 14%;">Seat Types</th>
                    <th style="width: 5%;">Baby</th>
                    <th style="width: 7%;">Amount</th>
                    <th style="width: 9%;">Booked At</th>
                    <th style="width: 8%;">Check-in</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>#{{ $row['table_no'] }}</td>
                        <td>{{ $row['name'] ?: '-' }}</td>
                        <td>{{ $row['email'] ?: '-' }}</td>
                        <td>{{ $row['phone'] ?: '-' }}</td>
                        <td>{{ $row['type'] === 'full_table' ? 'Full Table' : 'Seats' }}</td>
                        <td>{{ $row['seats'] }}</td>
                        <td>{{ $row['seat_types'] }}</td>
                        <td>{{ $row['baby_sitting'] }}</td>
                        <td class="right">${{ number_format((float) $row['amount'], 2) }}</td>
                        <td>
                            @if ($row['booked_at'])
                                {{ $row['booked_at']->format('M d, Y') }}
                                <div class="small">{{ $row['booked_at']->format('h:i A') }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($row['checked_in_at'])
                                {{ $row['checked_in_at']->format('M d, Y') }}
                                <div class="small">{{ $row['checked_in_at']->format('h:i A') }}</div>
                            @else
                                Not checked
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center; padding: 28px; color: #64748b;">
                            No booking records found for this event.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
