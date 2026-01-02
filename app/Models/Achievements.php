<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievements extends Model
{
    protected $guarded = [];

    protected $casts = [
        'card_desc' => 'array', // ✅ VERY IMPORTANT
    ];
}
