<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // Get authenticated user
    public function getUser(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'user' => $user
        ]);
    }

    // Update user profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'dob' => 'nullable|date',
            'telephone' => 'nullable|string|max:20',
            'genre' => 'nullable|string|in:Homme,Femme',
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'photo' => 'nullable|url',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $request->input('name', $user->name);
        $user->lname = $request->input('lname', $user->lname);
        $user->email = $request->input('email', $user->email);
        $user->dob = $request->input('dob', $user->dob);
        $user->telephone = $request->input('telephone', $user->telephone);
        $user->genre = $request->input('genre', $user->genre);
        $user->gouvernorat = $request->input('gouvernorat', $user->gouvernorat);
        $user->ville = $request->input('ville', $user->ville);

        if ($request->filled('photo')) {
            $user->photo = $request->input('photo');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user,
        ]);
    }

    // ✅ Upload profile photo and return public URL
    public function uploadPhoto(Request $request)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
    ]);
    
    $user = Auth::user();

    // Delete old photo if it exists
    if ($user->photo) {
        // Ensure the old photo exists before deleting it
        $oldPhotoPath = str_replace(url('storage'), public_path('storage'), $user->photo);
        if (file_exists($oldPhotoPath)) {
            unlink($oldPhotoPath);
        }
    }

    // Store the new photo in the 'public' disk directory under 'profile_photos'
    $path = $request->file('photo')->store('profile_photos', 'public');  // Ensure 'public' disk is used
    $photoUrl = asset('storage/' . $path);  // Generate the URL using the public disk

    // Save the new photo URL to the database
    $user->photo = $photoUrl;
    $user->save();

    return response()->json([
        'message' => 'Photo mise à jour avec succès.',
        'photoUrl' => $photoUrl,
    ]);
}

}
