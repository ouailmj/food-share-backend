<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'from','to','message','is_read'
    ];
}
