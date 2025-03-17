<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    // Register user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Hash the password before saving
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'user',
            
        ]);

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user
        ]);
    }

    // Login user
  // in UserController
public function login(Request $request)
{
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid email or password'], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken; // Make sure this is working correctly

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
    ]);
}

   
    public function getUser(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        return response()->json(['user' => $user]);
    }
    


    // Logout user
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    // Update user
   // in UserController
  // app/Http/Controllers/UserController.php

public function update(Request $request)
{
    $user = auth()->user();

    // Validation for the new fields
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'lname' => 'required|string|max:255',  // Last name is now required
        'email' => 'required|email|max:255',
        'photo' => 'nullable|string',
        'dob' => 'nullable|date',  // Date of birth is nullable but should be a valid date
        'telephone' => 'nullable|string|max:15',  // Phone number
        'gender' => 'nullable|in:Homme,Femme',  // Gender selection (Homme or Femme)
        'governorate' => 'nullable|string',  // Governorate selection
        'city' => 'nullable|string',  // City selection
    ]);

    // Update the user with the validated data
    $user->update($validatedData);

    return response()->json(['message' => 'Profile updated successfully!', 'user' => $user]);
}

    
}
