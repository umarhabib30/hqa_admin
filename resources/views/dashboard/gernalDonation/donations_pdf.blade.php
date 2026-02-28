<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Donations Report</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 13px;
            /* Slightly smaller for better PDF fit */
            margin: 20px;
            line-height: 1.4;
            background: #ffffff;
        }

        .report-card {
            border: 1px solid #dbeafe;
            background: linear-gradient(135deg, #eef4ff, #f8fbff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .title {
            font-size: 28px;
            font-weight: 800;
            color: #0b1f44;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            color: #475569;
            margin-top: 5px;
        }

        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 20px;
        }

        .summary-box {
            border-radius: 10px;
            padding: 15px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 800;
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
            table-layout: fixed;
            /* Forced column widths */
        }

        thead th {
            background: #0b1f44;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            padding: 10px;
            text-align: left;
        }

        tbody td {
            padding: 10px;
            border-top: 1px solid #e2e8f0;
            vertical-align: top;
            word-wrap: break-word;
            /* Prevents overflow */
            overflow: hidden;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Custom Styles for Stacking */
        .primary-text {
            display: block;
            font-weight: 700;
            color: #0f172a;
        }

        .secondary-text {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .amount {
            font-weight: 800;
            color: #0b1f44;
        }

        .mode-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="report-card">
        <h1 class="title">Donations Report</h1>

        <p class="subtitle">Date range: {{ $rangeLabel ?? 'All dates' }}</p>
    </div>

    <table class="summary-grid">
        <tr>
            <td class="summary-box">
                <div class="summary-label">Records</div>
                <div class="summary-value">{{ $donations->count() }}</div>
            </td>
            <td class="summary-box">
                <div class="summary-label">Total Amount</div>
                <div class="summary-value">${{ number_format((float) $donations->sum('amount'), 2) }}</div>
            </td>
            <td class="summary-box">
                <div class="summary-label">Average</div>
                <div class="summary-value">
                    ${{ number_format((float) ($donations->count() ? $donations->sum('amount') / $donations->count() : 0), 2) }}
                </div>
            </td>
        </tr>
    </table>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 7%;">ID/Date</th>
                    <th style="width: 23%;">Donor & Email</th>
                    <th style="width: 20%;">Purpose & Goal</th>
                    <th style="width: 12%;" class="text-right">Amount</th>
                    <th style="width: 13%;">Mode/Freq</th>
                    <th style="width: 25%;">Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donations as $donation)
                    <tr>
                        <td>
                            <span class="primary-text">#{{ $donation->id }}</span>
                            <span class="secondary-text">{{ optional($donation->created_at)->format('M d, Y') }}</span>
                        </td>
                        <td>
                            <span class="primary-text">{{ $donation->name ?: 'Anonymous' }}</span>
                            <span class="secondary-text">{{ $donation->email ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="primary-text">{{ $donation->donation_for ?: '-' }}</span>
                            <span class="secondary-text">{{ $donation->goal?->goal_name ?: 'No Goal' }}</span>
                        </td>
                        <td class="text-right">
                            <span class="amount">${{ number_format((float) $donation->amount, 2) }}</span>
                        </td>
                        <td>
                            <span class="mode-badge">{{ $donation->donation_mode ?: '-' }}</span>
                            <span class="secondary-text">{{ $donation->frequency ?: 'one_time' }}</span>
                        </td>
                        <td style="font-size: 11px; color: #475569;">
                            {{ $donation->address1 }}
                            {{ $donation->city ? ", $donation->city" : '' }}
                            {{ $donation->state ? ", $donation->state" : '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 40px; color:#94a3b8;">
                            No donation records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>

</html>
