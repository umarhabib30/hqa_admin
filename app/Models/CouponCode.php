<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    protected $fillable = [
        'coupon_id',
        'coupon_code',
        'is_used',
        'used_by_email',
        'used_at',
        'is_copied',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'is_copied' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Get the coupon that this code belongs to
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
