<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'used_by_email',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Get the coupon that this usage belongs to
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
