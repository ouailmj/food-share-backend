<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'objet','message'
    ];
}
