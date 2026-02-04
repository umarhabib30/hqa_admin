<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class feePersonPrice extends Model
{
    protected $fillable = ['event_id', 'title', 'price', 'is_active'];

    public function event()
    {
        return $this->belongsTo(PtoEvents::class, 'event_id');
    }
}
