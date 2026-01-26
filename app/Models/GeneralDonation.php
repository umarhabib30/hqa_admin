<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralDonation extends Model
{
    protected $fillable = [
        'fund_raisa_id',
        'name',
        'email',
        'amount',
        'payment_id',
        'donation_mode',
        'frequency',
        'stripe_customer_id',
        'stripe_subscription_id',
        'status'
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(FundRaisa::class, 'fund_raisa_id');
    }
}
