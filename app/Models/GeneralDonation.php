<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralDonation extends Model
{
    protected $fillable = ['name', 'email', 'amount', 'payment_id'];
}
