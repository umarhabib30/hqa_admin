<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTO Event Registration Confirmation</title>
</head>
<body style="margin:0; padding:24px; background:#f6f7fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#00285E; border-radius:16px; padding:22px; color:#fff;">
            <h1 style="margin:0 0 8px 0; font-size:22px;">🎉 Registration confirmed</h1>
            <p style="margin:0; font-size:14px; opacity:0.95;">You're registered for the PTO event.</p>
        </div>

        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; margin-top:14px; padding:24px;">
            <p style="color:#374151; margin:0 0 16px 0;">Hi {{ $attendee->first_name ?? 'there' }},</p>
            <p style="color:#374151; margin:0 0 20px 0;">Your registration for the following event is confirmed:</p>

            <p style="font-size:18px; font-weight:700; color:#00285E; margin:0 0 16px 0;">{{ $event->title ?? 'PTO Event' }}</p>

            @if($event)
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                @if($event->start_date)
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Date</td>
                    <td style="padding:6px 0; color:#111827;">{{ $event->start_date }}@if($event->end_date) – {{ $event->end_date }}@endif</td>
                </tr>
                @endif
                @if($event->start_time)
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Time</td>
                    <td style="padding:6px 0; color:#111827;">{{ $event->start_time }}@if($event->end_time) – {{ $event->end_time }}@endif</td>
                </tr>
                @endif
                @if(!empty($event->location))
                <tr>
                    <td style="padding:6px 0; color:#6b7280; font-size:13px;">Location</td>
                    <td style="padding:6px 0; color:#111827;">{{ $event->location }}</td>
                </tr>
                @endif
            </table>
            @endif

            <p style="color:#374151; margin:0 0 8px 0;"><strong>Guests:</strong> {{ $attendee->number_of_guests ?? 0 }}</p>
            <p style="color:#374151; margin:0 0 8px 0;"><strong>Amount paid:</strong> ${{ number_format((float)($attendee->amount ?? 0), 2) }}</p>

            <p style="margin-top:24px; color:#6b7280; font-size:13px;">We look forward to seeing you there.</p>
        </div>

        <p style="margin-top:16px; text-align:center; color:#9ca3af; font-size:12px;">{{ config('app.name') }}</p>
    </div>
</body>
</html>
