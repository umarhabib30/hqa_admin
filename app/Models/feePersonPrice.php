<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class feePersonPrice extends Model
{
    protected $guarded = [];

    public function easyJoins()
    {
        return $this->hasMany(EasyJoin::class);
    }
}
