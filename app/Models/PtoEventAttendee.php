<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PtoEventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'number_of_guests',
        'amount',      // Added
        'payment_id',   // Added
        'profile_pic',
    ];

    public function event()
    {
        return $this->belongsTo(PtoEvents::class);
    }
}
