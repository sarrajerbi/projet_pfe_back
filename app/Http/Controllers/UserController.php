<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
   


    public function register (Request $Request){
       return  $user= User ::create([
            'name'=>$Request->input('name'),
            'email'=>$Request->input('email'),
            'password'=>$Request->input('password'),
        ]);
        
    }


    public function login (Request $REQUEST){
        if (!Auth::attempt($REQUEST->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
          $user = User::where('email', $REQUEST->email)->firstOrFail();

          $token = $user->createToken('auth_token')->plainTextToken;
    

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
    ], 200);
}  

public function user()
{
    return Auth::user();
}

public function logout(Request $REQUEST)
{
    $REQUEST->user()->currentAccessToken()->delete(); 
    return response()->json(['message' => 'Déconnexion réussie']);
}
}