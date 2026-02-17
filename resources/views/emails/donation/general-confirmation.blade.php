@php
    $isRecurring = $donation->frequency !== 'one_time';
    $frequencyLabel = $isRecurring
        ? (($donation->frequency === 'month' ? 'Monthly' : ($donation->frequency === 'year' ? 'Yearly' : ucfirst($donation->frequency ?? 'Recurring'))))
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
    <title>Donation Confirmation</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">Thank you for your donation</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">Your {{ $frequencyLabel }} donation has been received.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0;">Hi {{ $donation->name ?: 'there' }},</p>
            <p style="color:#374151; margin:0 0 20px 0;">We have received your donation. Thank you for your generosity.</p>

            <p style="color:#374151; margin:0 0 8px 0;"><strong>Donation summary</strong></p>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Amount</td>
                    <td style="padding:6px 0; color:#111827; font-weight:600;">${{ number_format((float) $donation->amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Type</td>
                    <td style="padding:6px 0; color:#111827;">{{ $frequencyLabel }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Purpose</td>
                    <td style="padding:6px 0; color:#111827;">{{ $donation->donation_for ?? '—' }}</td>
                </tr>
                @if(!empty($honorLine))
                    <tr>
                        <td style="padding:6px 0; color:#6b7280; font-size:13px;">Honor</td>
                        <td style="padding:6px 0; color:#111827;">{{ $honorLine }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Date</td>
                    <td style="padding:6px 0; color:#111827;">{{ $donation->created_at?->format('M d, Y') ?? '—' }}</td>
                </tr>
            </table>

            @if(!empty($payload) && is_array($payload))
                <p style="color:#374151; margin:0 0 8px 0;"><strong>Details</strong></p>
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

            @if($isRecurring)
                <p style="color:#374151; margin:0 0 8px 0;">Your card will be charged according to your chosen schedule. You can manage or cancel your subscription from your Stripe customer portal or by contacting us.</p>
            @endif

            <p style="margin-top:24px; color:#6b7280; font-size:13px;">Thank you for supporting our cause.</p>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">{{ config('app.name') }}</p>
    </div>
</body>
</html>
