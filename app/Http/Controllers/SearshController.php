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
            $idCat = $this->request['categorie_id'];
            $idFilter = $this->request['filter'];
            $date = new \DateTime();
            $formatted_date = '';
            if($idFilter==1){
                $date->modify('-24 hours');
                $formatted_date = $date->format('Y-m-d H:i:s');
            }else if($idFilter==2){
                $date->modify('-7 day');
                $formatted_date = $date->format('Y-m-d H:i:s');
            }else if($idFilter==3){
                $date->modify('-1 month');
                $formatted_date = $date->format('Y-m-d H:i:s');
            }
            if($idCat==0){
                $q->where('annonces.created_at','>',$formatted_date)->where('title','like','%'.$query.'%')->orWhere('description','like','%'.$query.'%');
            }else{
                $q->where('annonces.created_at','>',$formatted_date)->where('categorie_id',$idCat)->where('title','like','%'.$query.'%')->orWhere('description','like','%'.$query.'%');
            }
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
