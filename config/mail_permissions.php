<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mail Permission Mapping
    |--------------------------------------------------------------------------
    |
    | Central mapping between high-level modules and the permission that
    | should receive internal "admin" style notification emails.
    |
    | These keys are used from controllers via config('mail_permissions.<key>').
    | Permission names come from Database\Seeders\PermissionSeeder.
    |
    */

    // Alumni related flows – use main Alumni access permission
    // so anyone who can access Alumni in admin gets all Alumni emails.
    'alumni_events' => 'alumni.view',
    'alumni_event_attendees' => 'alumni.view',
    'alumni_forms' => 'alumni.view',

    // General donations
    'donations' => 'donation.view',

    // PTO (Parent Teacher Organization)
    'pto_events' => 'pto.events',
    'pto_event_attendees' => 'pto.attendees',

    // Career / Jobs
    'job_applications' => 'career.job_applications',

    // Sponsors
    'sponsor_packages' => 'sponsor_packages.view',
    'contact_sponsor' => 'contact_sponsor.view',

    // Donation Booking (scan check-in)
    'donation_booking' => 'donation.booking',
];