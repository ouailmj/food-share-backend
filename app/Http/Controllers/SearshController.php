<?php

namespace App\Http\Controllers;

use App\model\Annonce;
use Illuminate\Http\Request;

class SearshController extends Controller
{
    public $request;
    public function search(Request $request){
        $this->request = $request->all();
        $annonces = Annonce::where('status',1)->where(function ($q){
            $query = $this->request['query'];
                $q->orwhere('title','like','%'.$query.'%')->orWhere('description','like','%'.$query.'%');
        })
            ->leftJoin('images', function ($join){
                $join->on('annonces.id', '=', 'images.annonce_id');
            })
            ->join('categories', 'annonces.categorie_id', '=', 'categories.id')
            ->select('annonces.*','images.url','categories.name')
            ->groupBy('annonces.id')
            ->get();
        return response()->json($annonces);
    }
}
