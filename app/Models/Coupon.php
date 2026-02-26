<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_name',
        'coupon_type',
        'discount_price',
        'discount_percentage',
        'quantity',
        'seats_allowed',
    ];

    protected $casts = [
        'discount_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'seats_allowed' => 'integer',
    ];

    /**
     * Get all coupon codes for this coupon
     */
    public function couponCodes()
    {
        return $this->hasMany(CouponCode::class);
    }

    /**
     * Get the number of times this coupon has been used
     */
    public function getUsedCountAttribute()
    {
        return $this->couponCodes()->where('is_used', true)->count();
    }

    /**
     * Get remaining uses
     */
    public function getRemainingCountAttribute()
    {
        return $this->couponCodes()->where('is_used', false)->count();
    }

    /**
     * Check if coupon is fully used
     */
    public function isFullyUsed()
    {
        return $this->used_count >= $this->quantity;
    }
}
