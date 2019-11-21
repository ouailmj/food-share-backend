<?php

namespace App\Http\Controllers;

use App\model\Annonce;
use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public function getDonnation(){
        $user = auth()->user();
        $clotured = Annonce::
            where('status',0)
            ->where('user_id',$user->id)->orderby('created_at','DESC')->get();
        $non_clotured = Annonce::
            where('status',1)
            ->where('user_id',$user->id)->orderby('created_at','DESC')->get();
        return response()->json(['clotured'=>$clotured,'non_clotured'=>$non_clotured],200);
    }
}
