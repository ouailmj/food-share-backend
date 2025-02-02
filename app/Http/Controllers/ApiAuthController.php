<?php

namespace App\Http\Controllers;

use App\model\Image;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public $user;
    public $token;
    public function register(Request $request){
        $validatedData = Validator::make($request->all(), [
            'first_name'=> 'required|max:55',
            'last_name'=> 'required|max:55',
            'phone'=> 'required|numeric',
            'date_naissance'=> 'required|date_format:Y-m-d',
            'email'=>'email|required|unique:users',
            'password'=>'required|confirmed|min:6'
        ]);
        if($validatedData->fails()){
            return response()->json(['error'=>$validatedData->errors()],401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        $input['passwordtoken'] = mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9);
        $this->token = $input['passwordtoken'];
        $user = User::create($input);
        $this->user = $user;
        $accessToken = $user->createToken('authToken')->accessToken;
        if(0){
            Mail::send([], [], function ($message) {
                $message->to($this->user->email)
                    ->subject('Email verification')
                    ->setBody('Veuillez verifier votre compte avec ce code :'.$this->token); // assuming text/plain
            });
        }else{
            $nexmo = app('Nexmo\Client');
            $nexmo->message()->send([
                'to'=>'+212'.(int) $input['phone'],
                'from'=>'+212642215381',
                'text'=> 'Veuillez verifier votre compte avec ce code :'.$this->token
            ]);
        }
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
        if(auth()->user()->nombre_signalisation > 3 || !auth()->user()->verified){
            return response()->json(['error'=>'Votre compte a été bloqué'],401);
        }
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $user = auth()->user();
        $image = Image::where('id',$user->image_id)->get();
        return response()->json(['user'=>auth()->user(),'image' => $image, 'access_token'=>$accessToken, 'expires_in'=> strtotime('+30 day', Carbon::now()->timestamp)],200);
    }


    public function getUser(){
        $user = auth()->user();
        $image_profile = $user->image();
        return response()->json(['success' => $user,'image_profile'=>$image_profile->get()],200);
    }

    public function update_profile_photo(Request $request){
        $user = auth()->user();
        $oldImages = Image::where(['user_id'=>$user->id])->get();
        if($oldImages){
            foreach ($oldImages as $value) {
                Image::destroy($value->id);
            }
        }
        if ($request->img!=null){
            $pic = new Image();
            $pic->url = $request->img;
            $pic->user_id = $user->id;
            $pic->save();
            $user->update([
                'image_id'=>$pic->id
            ]);
        }
        return response()->json(['message'=>'Image updated successfully'],200);
    }

    public function verifieAccount(Request $request){
        $input = $request->all();
        $user = auth()->user();
        if($user == null || $user->passwordtoken == ''){
            return response()->json(['message'=>'user not found'],404);
        }
        if($user->passwordtoken == $input['code']){
            $user->passwordtoken = '';
            $user->verified = true;
            $user->save();
            return response()->json(['message'=>'Votre compte a eté valider avec success'],200);
        }
        return response()->json(['message'=>'account does not existe'],401);
    }

    public function desactivateAcc(Request $request){
        $input = $request->all();
        $user = auth()->user();
        if($user->email == $input['email']){
            $user->update([
                'verified' => 0
            ]);
            return response()->json(['message'=>'desactivé'],200);
        }
    }


    public function updateUserDetails(Request $request){
            $validatedData = Validator::make($request->all(), [
                'first_name'=> 'required|max:55',
                'last_name'=> 'required|max:55',
                'date_naissance'=> 'required|date_format:Y-m-d',
                'email'=>'email',
            ]);
            if($validatedData->fails()){
                return response()->json(['error'=>$validatedData->errors()],401);
            }
            $input = $request->all();

            $user = User::where('email',$input['email'])->update([
                'first_name'=>$input['first_name'],
                'last_name'=>$input['last_name'],
                'sexe' => ($input['sexe']=='male'),
                'ville' => $input['ville'],
                'address' => $input['address'],
                'date_naissance' => $input['date_naissance'],
            ]);
        return response()->json(['user'=>$user],200);
    }

}
