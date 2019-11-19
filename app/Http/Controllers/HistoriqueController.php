<?php

namespace App\Http\Controllers;

use App\model\Annonce;
use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public function getDonnation($id){
        $user = auth()->user();
        $clotured = Annonce::all()
            ->where('status',0)
            ->where('user_id',$user->id)->get();
        $non_clotured = Annonce::all()
            ->where('status',1)
            ->where('user_id',$user->id)->get();
        return response()->json(['clotured'=>$clotured,'non_clotured'=>$non_clotured],200);
    }
}
