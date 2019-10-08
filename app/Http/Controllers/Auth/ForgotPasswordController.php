<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use SendsPasswordResetEmails;
    public $token;
    public function sendResetLinkEmail(Request $request)
    {
        $input= $request->all();
        $user = User::where('email',$input['email'])->firstOrFail();
        if($user == null){
            return response()->json(['message'=>'user not found'],404);
        }
        $this->token = 'APP-'.rand(1, 100000);
        $user->passwordtoken = $this->token;
        $user->save();
        Mail::send([], [], function ($message) {
            $message->to('ouailmjahedooo@gmail.com')
            ->subject('Reset password')
            ->setBody('Hey, ure new password is '.$this->token); // assuming text/plain
        });

        return response()->json(['message'=>'email sent successfully'],200);
    }

    public function resetPassword(Request $request){
        $input= $request->all();
        $user = User::where(['email'=>$input['email'],'passwordtoken'=>$input['passwordtoken']])->firstOrFail();
        if($user == null || $user->passwordtoken == ''){
            return response()->json(['message'=>'user not found'],404);
        }
        $user->passwordtoken = '';
        $user->password = bcrypt($input['password']);
        $user->save();
        return response()->json(['message'=>'password updated successfully'],200);
    }

}
