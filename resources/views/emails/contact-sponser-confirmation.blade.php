<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>We received your sponsor inquiry</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">Thank you for reaching out</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">We’ve received your sponsor contact request.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0;">Hi {{ $contact->full_name ?? 'there' }},</p>
            <p style="color:#374151; margin:0 0 20px 0;">
                Thank you for your interest in supporting {{ config('app.name') }}. Our team will review your message and get back to you soon.
            </p>

            <p style="color:#374151; margin:0 0 8px 0;"><strong>Your details</strong></p>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Full Name</td>
                    <td style="padding:6px 0; color:#111827;">{{ $contact->full_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Email</td>
                    <td style="padding:6px 0; color:#111827;">{{ $contact->email ?? '-' }}</td>
                </tr>
                @if(!empty($contact->company_name))
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Company</td>
                    <td style="padding:6px 0; color:#111827;">{{ $contact->company_name }}</td>
                </tr>
                @endif
                @if(!empty($contact->sponsor_type))
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Sponsor Type</td>
                    <td style="padding:6px 0; color:#111827;">{{ $contact->sponsor_type }}</td>
                </tr>
                @endif
            </table>

            <p style="margin-top:24px; color:#6b7280; font-size:13px;">If you didn’t submit this request, you can ignore this email.</p>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">{{ config('app.name') }} – Sponsorships</p>
    </div>
</body>
</html>

