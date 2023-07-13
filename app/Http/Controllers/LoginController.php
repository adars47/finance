<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    public function register(Request $request)
    {
        $validateData = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

        if($validateData->fails())
        {
            //throw an exception
            return response()->json($validateData->errors());
        }

        $user = new User();
        $user->email=$request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            "title"=>"User registered",
            "body"=>"Successfully registered"
        ]);
    }

    public function login(Request $request)
    {

        $validateData = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

        if($validateData->fails())
        {
            //throw an exception
            return $request->json($validateData->errors());
        }

        $user = User::query()->where("email",$request->email)->first();
        if($user==null)
        {
            return response()->json([
                "title"=>"Failed login",
                "body"=>"Failed to login"
            ]);

        }


        if(Hash::check($request->password,$user->password))
        {
            $token = $user->createToken('basic');

            return response()->json([
                "title"=>"Logged in",
                "body"=>$token->plainTextToken
            ]);
        }
        return response()->json([
            "title"=>"Failed login",
            "body"=>"Failed to login"
        ]);

    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json([
            "title"=>"Logged out",
            "body"=>"Successfully logged out"
        ]);
    }
}
