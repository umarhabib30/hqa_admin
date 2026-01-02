<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtoImage extends Model
{
    protected $guarded = [];
    protected $casts = [
        'images' => 'array',
    ];
}
