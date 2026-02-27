<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Form Confirmation</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">Thank you for your submission</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">We have received your alumni form.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0;">Hi {{ $form->first_name }} {{ $form->last_name }},</p>
            <p style="color:#374151; margin:0 0 20px 0;">Thank you for sharing your information with {{ config('app.name') }}. Our team will review your submission.</p>

            <p style="color:#374151; margin:0 0 8px 0;"><strong>Summary</strong></p>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Name</td>
                    <td style="padding:6px 0; color:#111827;">{{ $form->first_name }} {{ $form->last_name }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Email</td>
                    <td style="padding:6px 0; color:#111827;">{{ $form->email }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Graduation Year</td>
                    <td style="padding:6px 0; color:#111827;">{{ $form->graduation_year }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Status</td>
                    <td style="padding:6px 0; color:#111827;">{{ $form->status }}</td>
                </tr>
                @if(!empty($form->college))
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">College</td>
                    <td style="padding:6px 0; color:#111827;">{{ $form->college }}</td>
                </tr>
                @endif
                @if(!empty($form->degree))
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Degree</td>
                    <td style="padding:6px 0; color:#111827;">{{ $form->degree }}</td>
                </tr>
                @endif
            </table>

            <p style="margin-top:24px; color:#6b7280; font-size:13px;">If you didn’t submit this form, please ignore this email.</p>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">{{ config('app.name') }} – Alumni</p>
    </div>
</body>
</html>

