<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request){
        $user = new User();
        $user->name = $request->name;
        $user->email= $request->email;
        $user->password =bcrypt($request->password) ;

        $user->save();

        // // create token when create account 
            // $token = $user->createToken('mytoken')->plainTextToken;
            // return response()->json(['user'=>$user,'token'=>$token]);

        return response()->json(['sms'=>'created']);
    }

    public function login(Request $request){

        // // ONE Ways by using Hash

        // $user = User::where('email',$request->email)->first();
        // if(!$user || !Hash::check($user->password,$user->password)){
        //     return response()->json(['sms'=>'Invalid password'],401);
        // }
        // $token = $user->createToken('mytoken')->plainTextToken;
        // return response()->json(['user'=>$user,'token'=>$token]);

        // // Ways two by using Auth 

        if(!Auth::attempt($request->only('email','password'))){
            return response()->json(['sms'=>'Invalid credentials'],402);
        }
        $user  = Auth::user();
        $token = $user->createToken('mytoken')->plainTextToken;
        $cookie = cookie('jwt',$token,60*24); // 1day if morethan it can use cookie
        return response()->json(['sms'=>'success','user'=>$user,'token'=>$token],200) ->withCookie($cookie)    ;   
    }

    public function logout(Request $request){
        // // Ways one 

        // auth()->user()->tokents()->delete();
        // return response()->json(['sms'=>'log out success']);

        // // Ways two with cookie
        
        $cookie = Cookie::forget('jwt');
        return response()->json(['sms'=>'logged out'])
                         ->withCookie($cookie);   
    }
}
