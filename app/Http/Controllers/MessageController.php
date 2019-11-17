<?php

namespace App\Http\Controllers;

use App\Events\MessageSubmited;
use App\model\Image;
use App\model\Message;
use App\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function getChat(){
        $user = auth()->user();
        $a=array();
        $messages = Message::latest()->where('from',$user->id)->orWhere('to',$user->id)->latest()->groupBy('to','from')->orderby('created_at','desc')->paginate(10);
        $x = [];
        foreach ($messages as $mes){
            $userMes = [];
            $completeArray = [];
            $image = '';
            $ok = true;
            array_push($x ,$mes->to.$mes->from);
            foreach($x as $y){
                if($mes->from.$mes->to == $y){
                    $ok = false;
                }
            }
            if($ok){
                if($mes->from == $user->id){
                    $userMes = User::where('id',$mes->to)->get();
                }else{
                    $userMes = User::where('id',$mes->from)->get();
                }
                $image = Image::where('id',$userMes[0]->image_id)->get();
                $completeArray['user'] = $userMes;
                $completeArray['message'] = $mes;
                $completeArray['image'] = $image;
                array_push($a,$completeArray);
            }
        }
        return response()->json(['messages'=>$a],200);
    }

    public function getMessage($id){
        $user = auth()->user();
        $messages = Message::where(['from' => $user->id, 'to' => $id ])->orWhere(['from' => $id, 'to' => $user->id])->orderBy('created_at', 'DESC')->get();
        $otherUser = User::findOrFail($id);
        $image = Image::where('id',$otherUser->image_id)->get();
        return response()->json(['messages'=>$messages,'user'=>$otherUser,'image'=>$image],200);
    }

    public function submit(Request $request){
        $message = new Message();
        $message->from = $request->from;
        $message->to = $request->to;
        $message->message = $request->message;
        $message->save();
        event(new MessageSubmited($request->message,$request->from,$request->to,'message'));
        return response()->json(['messages'=>"message submitted"],200);
    }

    public function getUser($id){
        $userMes = User::findOrFail($id)->get();
        $image = Image::where('id',$userMes[0]->image_id)->get();
        return response()->json(['user'=>$userMes,'image'=>$image],200);
    }
}
