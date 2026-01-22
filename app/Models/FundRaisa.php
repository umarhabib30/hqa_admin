<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundRaisa extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function generalDonations(): HasMany
    {
        return $this->hasMany(GeneralDonation::class, 'fund_raisa_id');
    }
}
