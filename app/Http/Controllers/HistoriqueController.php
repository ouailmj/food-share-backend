<?php

namespace App\Http\Controllers;

use App\model\Annonce;
use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public function getDonnation($id){
        $clotured = Annonce::all()
            ->where('status',0)
            ->where('user_id',$id);
        $non_clotured = Annonce::all()
            ->where('status',1)
            ->where('user_id',$id);
        return response()->json(['clotured'=>$clotured,'non_clotured'=>$non_clotured],200);
    }
}
