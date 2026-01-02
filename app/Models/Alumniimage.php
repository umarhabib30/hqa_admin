<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumniimage extends Model
{
    protected $guarded = [];
    protected $casts = [
        'images' => 'array',
    ];
}
