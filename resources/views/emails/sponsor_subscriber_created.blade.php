<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sponsor Subscriber</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:720px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px 22px 18px 22px; color:#fff;">
            <div style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; opacity:0.9;">
                Sponsor Packages
            </div>
            <h2 style="margin:6px 0 0 0; font-size:20px; line-height:1.25;">
                New Sponsor Package Subscriber
            </h2>
            <div style="margin-top:8px; font-size:13px; opacity:0.9;">
                A new subscriber has been created in your system.
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; overflow:hidden;">
            <div style="padding:20px;">
                <table style="width:100%; border-collapse:separate; border-spacing:0 10px;">
                    <tr>
                        <td style="width:180px; color:#6b7280; font-size:12px;">Package</td>
                        <td style="color:#111827; font-weight:700;">
                            {{ $subscriber->package?->title ?? ($subscriber->sponsor_type ?: '-') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280; font-size:12px;">Name</td>
                        <td style="color:#111827;">{{ $subscriber->user_name ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280; font-size:12px;">Email</td>
                        <td style="color:#111827;">{{ $subscriber->user_email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280; font-size:12px;">Phone</td>
                        <td style="color:#111827;">{{ $subscriber->user_phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280; font-size:12px;">Amount</td>
                        <td style="color:#111827; font-weight:700;">
                            ${{ number_format((float) ($subscriber->amount ?? 0), 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280; font-size:12px;">Status</td>
                        <td>
                            <span style="display:inline-block; padding:6px 10px; border-radius:999px; background:#ecfdf5; color:#065f46; font-size:12px; font-weight:700;">
                                {{ $subscriber->status ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280; font-size:12px;">Payment ID</td>
                        <td style="color:#111827; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size:12px;">
                            {{ $subscriber->payment_id ?: '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280; font-size:12px;">Created</td>
                        <td style="color:#111827;">
                            {{ $subscriber->created_at?->format('M d, Y h:i A') ?? '-' }}
                        </td>
                    </tr>
                </table>

                <div style="margin-top:18px; padding-top:14px; border-top:1px solid #e5e7eb; color:#6b7280; font-size:12px;">
                    This is an automated notification from your admin dashboard.
                </div>
            </div>
        </div>
    </div>
</body>
</html>

