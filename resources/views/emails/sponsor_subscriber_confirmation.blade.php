<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship Confirmation</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">Thank you for your sponsorship</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">Your support means a lot to us.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0; line-height:1.5;">Hi {{ $subscriber->user_name ?? 'there' }},</p>
            <p style="color:#374151; margin:0 0 20px 0; line-height:1.5;">
                This email confirms your sponsorship: <strong>{{ $packageTitle }}</strong>.
            </p>

            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="padding:8px 0; color:#6b7280; font-size:13px;">Package</td>
                    <td style="padding:8px 0; color:#111827; font-weight:600;">{{ $packageTitle }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#6b7280; font-size:13px;">Amount</td>
                    <td style="padding:8px 0; color:#111827; font-weight:600;">${{ number_format((float) ($subscriber->amount ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#6b7280; font-size:13px;">Status</td>
                    <td style="padding:8px 0;"><span style="background:#ecfdf5; color:#065f46; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600;">{{ $subscriber->status ?? 'Paid' }}</span></td>
                </tr>
            </table>

            <p style="margin-top:24px; color:#6b7280; font-size:13px;">
                If you have any questions, please reply to this email or contact us.
            </p>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
