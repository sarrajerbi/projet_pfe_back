<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // For making HTTP requests
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        // Google OAuth 2.0 URL with the necessary parameters
        $googleUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'email profile',
            'state' => csrf_token(), // Prevent CSRF
        ]);

        return redirect($googleUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        // Validate the Google OAuth response
        if ($request->has('error')) {
            return response()->json(['error' => 'Google login failed.'], 400);
        }

        $code = $request->input('code');

        // Exchange the code for an access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to get Google access token.'], 400);
        }

        $accessToken = $response->json()['access_token'];

        // Use the access token to get the user's profile information
        $userProfileResponse = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
        ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if ($userProfileResponse->failed()) {
            return response()->json(['error' => 'Failed to fetch user profile.'], 400);
        }

        $googleUser = $userProfileResponse->json();

        // Find or create the user in your database
        $user = User::where('email', $googleUser['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser['name'],
                'email' => $googleUser['email'],
                'password' => bcrypt(str_random(16)), // Generate a random password for the user
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Return a response with user data and token
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken, // Using Laravel Sanctum
        ]);
    }
}
