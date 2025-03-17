<?php
namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FacebookAuthController extends Controller
{
    // Redirect to Facebook authentication
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Handle the callback from Facebook
   // In FacebookAuthController
public function handleFacebookCallback()
{
    $user = Socialite::driver('facebook')->user();

    // Check if the user exists
    $existingUser = User::where('facebook_id', $user->getId())->first();

    if ($existingUser) {
        Auth::login($existingUser, true);
        // Generate token for the user
        $token = $existingUser->createToken('YourAppName')->plainTextToken;
        
        // Return the token to the frontend
        return response()->json(['token' => $token]);
    } else {
        $newUser = User::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'facebook_id' => $user->getId(),
            'avatar' => $user->getAvatar(),
        ]);

        Auth::login($newUser, true);
        $token = $newUser->createToken('YourAppName')->plainTextToken;
        
        return response()->json(['token' => $token]);
    }
}

}
