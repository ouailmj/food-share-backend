<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'url','annonce_id','user_id','className'
    ];

    public function annonces(){
        return $this->belongsToMany('App\model\Annonce');
    }
}
