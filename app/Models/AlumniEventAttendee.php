<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlumniEventAttendee extends Model
{
    use HasFactory;

    protected $table = 'alumni_event_attendees';

    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'number_of_guests',
        'amount',
        'payment_id',
        'profile_pic',
    ];

    public function event()
    {
        return $this->belongsTo(AlumniEvent::class, 'event_id');
    }
}
