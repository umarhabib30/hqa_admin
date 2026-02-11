<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Alumni Event Registration</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">New alumni event registration</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">A new attendee has registered and paid via the API.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0;">Attendee details:</p>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Name</td>
                    <td style="padding:6px 0; color:#111827;">{{ $attendee->first_name ?? '' }} {{ $attendee->last_name ?? '' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Email</td>
                    <td style="padding:6px 0; color:#111827;">{{ $attendee->email ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Phone</td>
                    <td style="padding:6px 0; color:#111827;">{{ $attendee->phone ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Event</td>
                    <td style="padding:6px 0; color:#111827; font-weight:600;">{{ $event->title ?? 'Alumni Event' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Guests</td>
                    <td style="padding:6px 0; color:#111827;">{{ $attendee->number_of_guests ?? 0 }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Amount paid</td>
                    <td style="padding:6px 0; color:#111827; font-weight:600;">${{ number_format((float)($attendee->amount ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Registered at</td>
                    <td style="padding:6px 0; color:#111827;">{{ $attendee->created_at?->format('M d, Y \a\t H:i') ?? '—' }}</td>
                </tr>
            </table>

            <a href="{{ url('/admin/alumni-event-attendees') }}"
               style="display:inline-block; margin-top:16px; padding:12px 20px; background:#00285E; color:white; text-decoration:none; border-radius:8px; font-size:14px;">
                View alumni attendees in admin
            </a>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">{{ config('app.name') }} – Alumni Event Attendees</p>
    </div>
</body>
</html>
