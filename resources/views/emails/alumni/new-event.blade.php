<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Alumni Event</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">
    <div style="max-width:600px; background:#ffffff; padding:20px; margin:auto; border-radius:8px;">
        <h2 style="color:#00285E;">
            🎓 New Alumni Event Announced!
        </h2>

        <p>
            A new alumni event has been scheduled. Check the details below:
        </p>

        <hr>

        <p><strong>Title:</strong> {{ $event->title }}</p>

        <p><strong>Date:</strong>
            {{ $event->start_date }}
            @if($event->end_date) – {{ $event->end_date }} @endif
        </p>

        @if($event->start_time)
        <p><strong>Time:</strong>
            {{ $event->start_time }}
            @if($event->end_time) – {{ $event->end_time }} @endif
        </p>
        @endif

        @if($event->location)
        <p><strong>Location:</strong> {{ $event->location }}</p>
        @endif

        @if($event->description)
        <p><strong>Description:</strong><br>{{ $event->description }}</p>
        @endif

        <a href="{{ url('/login') }}"
            style="display:inline-block;margin-top:20px;
                  padding:12px 20px;
                  background:#00285E;
                  color:white;
                  text-decoration:none;
                  border-radius:6px;">
            View Event
        </a>

        <p style="margin-top:30px;font-size:12px;color:#888;">
            You are receiving this email because you subscribed to alumni updates.
        </p>
    </div>
</body>

</html>