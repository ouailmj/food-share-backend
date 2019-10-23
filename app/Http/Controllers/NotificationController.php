<?php

namespace App\Http\Controllers;

use App\model\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNotification(){
        $user = auth()->user();
        $notifications = Notification::where('user_to',$user->id)
            ->orderBy('created_at', 'DESC')->get();
        return response()->json(['notification' => $notifications],200);
    }
}
