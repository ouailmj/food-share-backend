<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    protected  $fillable = [
        'message','annonce_id','user_id'
    ];
}
