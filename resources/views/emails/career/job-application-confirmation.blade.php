<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Job Application Received</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">Application received</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">Thank you for applying for the teacher position.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0;">Hi {{ $application->first_name ?? 'there' }},</p>
            <p style="color:#374151; margin:0 0 20px 0;">We have received your teacher job application. Our team will review it and get back to you if your profile matches our requirements.</p>

            <p style="color:#374151; margin:0 0 8px 0;"><strong>Summary of your application:</strong></p>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Name</td>
                    <td style="padding:6px 0; color:#111827;">{{ $application->first_name }} {{ $application->last_name }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Email</td>
                    <td style="padding:6px 0; color:#111827;">{{ $application->email }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Phone</td>
                    <td style="padding:6px 0; color:#111827;">{{ $application->phone }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Years of experience</td>
                    <td style="padding:6px 0; color:#111827;">{{ $application->years_experience }}</td>
                </tr>
            </table>

            <p style="margin-top:24px; color:#6b7280; font-size:13px;">Thank you for your interest in joining our team.</p>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">{{ config('app.name') }}</p>
    </div>
</body>
</html>
