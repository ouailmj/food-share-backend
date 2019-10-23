<?php

namespace App\Http\Controllers;

use App\Events\CommentSubmitted;
use App\model\Annonce;
use App\model\Commentaire;
use App\model\Notification;
use App\User;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    public function get($id){
        $comments = \DB::table('commentaires')
            ->where('commentaires.annonce_id', $id)
            ->leftjoin('users', 'commentaires.user_id', '=', 'users.id')
            ->leftjoin('images', 'users.id', '=', 'images.user_id')
            ->select('images.url','users.*','commentaires.*')
            ->orderBy('commentaires.created_at', 'ASC')
            ->get();
        return response()->json(['commentaires' => $comments],200);
    }

    public function insert(Request $request){
        $comment = new Commentaire();
        $comment->user_id = $request->user_id;
        $comment->annonce_id = $request->id;
        $comment->message = $request->message;
        $comment->save();
        $notif = new Notification();
        $annonce = Annonce::findOrFail($request->id);
        $notif->user_from = $request->user_id;
        $notif->user_to = $annonce->user_id;
        $notif->objet = 'commentaire';
        $notif->message = 'Un utilisateur a commenter votre poste';
        $notif->annonce_id = $annonce->id;
        $notif->save();
        event(new CommentSubmitted($notif->user_from,$notif->user_to,$notif->annonce_id,'commentaire'));
        return response()->json(['message' => 'commentaire inserer avec succes'],200);
    }
    public function update(Request $request){
        Commentaire::where('id',$request->id)
            ->update(['message' => $request->message]);
        return response()->json(['message' => 'commentaire updated avec succes'],200);
    }
    public function delete($id){
        Commentaire::where('id',$id)->delete();
        return response()->json(['message' => 'commentaire supprimé'],200);
    }
}
