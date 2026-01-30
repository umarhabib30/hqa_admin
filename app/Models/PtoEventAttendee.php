<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PtoEventAttendee extends Model
{
    use HasFactory;

    // Allow mass assignment on these fields
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        // 'will_attend',
        'number_of_guests',
        'profile_pic',
    ];
}
