<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationBooking extends Model
{
    protected $guarded = [];

    // ✅ THIS IS REQUIRED
    protected $casts = [
        'ticket_types'     => 'array',
        'table_bookings'   => 'array',
        'allow_full_table' => 'boolean',
    ];
}
