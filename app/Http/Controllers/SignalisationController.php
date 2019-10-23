<?php

namespace App\Http\Controllers;

use App\model\Annonce;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignalisationController extends Controller
{
    public function getMotif(){
        $motifs = DB::table('signals')->get();
        return response()->json(['motifs'=>$motifs],200);
    }

    public function signaliserUtilisateur(Request $request){
        $annonce = Annonce::findOrFail($request->annonce_id);
        $user = User::findOrFail($annonce->user_id);
        $user->update([
           'nombre_signalisation' => $user->nombre_signalisation + 1
        ]);
        return response()->json(['message'=>"user a ete signalé"],200);
    }

}
