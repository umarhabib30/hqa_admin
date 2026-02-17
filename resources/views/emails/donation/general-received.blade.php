@php
    $isRecurring = $donation->frequency !== 'one_time';
    $typeLabel = $isRecurring
        ? 'Recurring (' . ($donation->frequency === 'month' ? 'Monthly' : ($donation->frequency === 'year' ? 'Yearly' : $donation->frequency)) . ')'
        : 'One-time';

    $honorLine = null;
    if (!empty($donation->honor_type) && !empty($donation->honor_name)) {
        $honorLine = $donation->honor_type === 'memory'
            ? 'In the memory of ' . $donation->honor_name
            : 'In the honor of ' . $donation->honor_name;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New General Donation</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">New general donation</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">A {{ $typeLabel }} donation was made via the API.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0;">Donation details:</p>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Donor name</td>
                    <td style="padding:6px 0; color:#111827;">{{ $donation->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Email</td>
                    <td style="padding:6px 0; color:#111827;">{{ $donation->email ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Amount</td>
                    <td style="padding:6px 0; color:#111827; font-weight:600;">${{ number_format((float) $donation->amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Type</td>
                    <td style="padding:6px 0; color:#111827;">{{ $typeLabel }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Purpose</td>
                    <td style="padding:6px 0; color:#111827;">{{ $donation->donation_for ?? '—' }}</td>
                </tr>
                @if(($donation->donation_for ?? null) === 'Other' && !empty($donation->other_purpose))
                    <tr>
                        <td style="padding:6px 0; color:#6b7280; font-size:13px;">Other purpose</td>
                        <td style="padding:6px 0; color:#111827;">{{ $donation->other_purpose }}</td>
                    </tr>
                @endif
                @if(!empty($honorLine))
                    <tr>
                        <td style="padding:6px 0; color:#6b7280; font-size:13px;">Honor</td>
                        <td style="padding:6px 0; color:#111827;">{{ $honorLine }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Submitted at</td>
                    <td style="padding:6px 0; color:#111827;">{{ $donation->created_at?->format('M d, Y \a\t H:i') ?? '—' }}</td>
                </tr>
            </table>

            @if(!empty($payload) && is_array($payload))
                <p style="color:#374151; margin:0 0 8px 0;"><strong>Payload details</strong></p>
                <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                    @foreach($payload as $k => $v)
                        <tr>
                            <td style="padding:6px 0; color:#6b7280; font-size:13px; width:180px;">{{ $k }}</td>
                            <td style="padding:6px 0; color:#111827; font-size:13px;">
                                @if(is_array($v))
                                    {{ json_encode($v) }}
                                @else
                                    {{ (string) $v }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <a href="{{ url('/admin/donations') }}"
               style="display:inline-block; margin-top:16px; padding:12px 20px; background:#00285E; color:white; text-decoration:none; border-radius:8px; font-size:14px;">
                View donations in admin
            </a>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">{{ config('app.name') }} – Donations</p>
    </div>
</body>
</html>
