<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponserPackageSubscriber extends Model
{
    protected $fillable = 
    ['user_name', 'user_email', 'user_phone', 'sponsor_package_id', 'sponsor_type', 'status', 'image', 'amount', 'payment_id'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(SponsorPackage::class, 'sponsor_package_id');
    }
}
