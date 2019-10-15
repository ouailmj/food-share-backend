<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $fillable = [
        'title','description','status','user_id','categorie_id','date_expiration','produit_avec_date_expiration'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }
    public function comentaires(){
        return $this->hasMany('App\model\Commentaire');
    }

    //many to many
    public function comments(){
        return $this->belongsToMany('App\User','commentaires');
    }

    public function images(){
        return $this->hasMany('App\model\Image');
    }

    public function categorie(){
        return $this->belongsTo('App\model\Categorie');
    }

}
