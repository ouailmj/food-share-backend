<?php

namespace App\Http\Controllers;

use App\Events\MessageSubmited;
use App\model\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function getChat(){
        $user = auth()->user();
        $messages = Message::where('from',$user->id)->orWhere('to',$user->id)->orderBy('created_at', 'DESC')->get();
        return response()->json(['messages'=>$messages],200);
    }

    public function getMessage($id){
        $user = auth()->user();
        $messages = Message::where(['from' => $user->id, 'to' => $id ])->orWhere(['from' => $id, 'to' => $user->id])->orderBy('created_at', 'DESC')->get();
        return response()->json(['messages'=>$messages],200);
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
}
