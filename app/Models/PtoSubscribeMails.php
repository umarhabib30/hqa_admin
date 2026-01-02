<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtoSubscribeMails extends Model
{
    protected $guarded = [];

    public function dashboard()
    {
        return $this->belongsTo(dashboard::class);
    }
}
