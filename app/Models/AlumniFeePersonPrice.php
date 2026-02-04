<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumniFeePersonPrice extends Model
{
    protected $fillable = ['event_id', 'title', 'price', 'is_active'];

    // Relationship with Alumni Event
    public function event()
    {
        return $this->belongsTo(\App\Models\AlumniEvent::class, 'event_id');
    }
}
