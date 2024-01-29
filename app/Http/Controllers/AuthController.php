<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    //
    public function register(Request $request)

    {
        $request->validate([

            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);

       $user = new User([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),

       ]);   

       if($user->save()){
        $tokenResult = $user->createToken('personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'message' => 'Successfully created User!',
            'accessToken' => $token,
        ],201);

       }
       else{
        return response()->json([
          'message' => 'Failed to create User!',
        ],401);
       }
    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);
        $credentials = request(['email','password']);
        if(!Auth::attempt($credentials)){
            return response()->json([
             'message' => 'Unauthorized'
            ],401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function user(Request $request){
        return response()->json(Auth::user());
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
          'message' => 'Successfully logged out'
        ]);
    }
    
}
