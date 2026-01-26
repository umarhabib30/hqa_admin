<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function subscribers(): HasMany
    {
        return $this->hasMany(SponserPackageSubscriber::class, 'sponsor_package_id');
    }
}
