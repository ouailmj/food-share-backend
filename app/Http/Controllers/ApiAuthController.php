<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function register(Request $request){
        $validatedData = Validator::make($request->all(), [
            'name'=> 'required|max:55',
            'email'=>'email|required|unique:users',
            'password'=>'required|confirmed|min:6'
        ]);
        if($validatedData->fails()){
            return response()->json(['error'=>$validatedData->errors()],401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        $user = User::create($input);
        $accessToken = $user->createToken('authToken')->accessToken;

        return response()->json(['user'=>$user, 'access_token'=>$accessToken, 'expires_in'=> strtotime('+30 day', Carbon::now()->timestamp)],200);
    }

    public function login(Request $request){
        $validatedData = $request->validate([
            'email'=>'email|required',
            'password'=>'required|min:6'
        ]);
        if(!auth()->attempt($validatedData)){
            return response()->json(['error'=>'Invalid credentials'],401);
        }
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response()->json(['user'=>auth()->user(), 'access_token'=>$accessToken, 'expires_in'=> strtotime('+30 day', Carbon::now()->timestamp)],200);
    }

    public function getUser(){
        $user = Auth::user();
        return response()->json(['success' => $user, 'expires_in'=> strtotime('+30 day', Carbon::now()->timestamp)],200);
    }
}
