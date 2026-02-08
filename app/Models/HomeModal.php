<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeModal extends Model
{
    protected $table = 'home_modals';

    protected $fillable = [
        'title',
        'cdesc',
        'image',
        'btn_text',
        'btn_link',
        'general_link'
    ];
}
