<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorPackage extends Model
{
    protected $fillable = [
        'title',
        'price_per_year',
        'benefits',
    ];

    protected $casts = [
        'benefits' => 'array',
        'price_per_year' => 'decimal:2',
    ];
}
