<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSponserModel extends Model
{
    protected $fillable = ['full_name','company_name', 'email','phone', 'sponsor_type', 'message'];
}
