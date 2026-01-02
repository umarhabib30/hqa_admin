<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopAchiever extends Model
{
    protected $guarded = [];
    protected $casts = [
        'meta_data' => 'array',
    ];
}
