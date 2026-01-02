<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dashboard extends Model
{
    public function mails()
    {
        return $this->hasMany(PtoSubscribeMails::class);
    }
}
