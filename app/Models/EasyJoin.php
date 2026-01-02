<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EasyJoin extends Model
{
    protected $guarded = [];

    public function fee()
    {
        return $this->belongsTo(feePersonPrice::class, 'fee_person_price_id');
    }
}
