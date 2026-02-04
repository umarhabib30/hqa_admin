<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlumniEvent extends Model
{
    use HasFactory;

    protected $table = 'alumni_events';

    protected $guarded = [];

    public function attendees()
    {
        return $this->hasMany(AlumniEventAttendee::class, 'event_id');
    }
}
