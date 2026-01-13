<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Sponsor Contact</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">
    <div style="max-width:600px; background:#ffffff; padding:20px; margin:auto; border-radius:8px;">

        <h2 style="color:#00285E;">
            New Sponsor Contact Request
        </h2>

        <p>
            A new sponsor contact form has been submitted. Details are below:
        </p>

        <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">

        <p><strong>Full Name:</strong> {{ $contact->full_name ?? '-' }}</p>
        <p><strong>Company Name:</strong> {{ $contact->company_name ?? '-' }}</p>
        <p><strong>Email:</strong> {{ $contact->email ?? '-' }}</p>
        <p><strong>Phone:</strong> {{ $contact->phone ?? '-' }}</p>
        <p><strong>Sponsor Type:</strong> {{ $contact->sponsor_type ?? '-' }}</p>

        @if(!empty($contact->message))
            <p><strong>Message:</strong><br>{{ $contact->message }}</p>
        @endif

        <a href="{{ url('/login') }}"
           style="display:inline-block;margin-top:20px;
                  padding:12px 20px;
                  background:#00285E;
                  color:white;
                  text-decoration:none;
                  border-radius:6px;">
            View in Admin Panel
        </a>

        <p style="margin-top:30px;font-size:12px;color:#888;">
            This email was sent automatically because a sponsor contact form was submitted.
        </p>
    </div>
</body>
</html>
