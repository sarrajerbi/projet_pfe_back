<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        // Fetch the logged-in user data
        $user = Auth::user();
        return response()->json($user);
    }

    public function addFavoris(Request $request)
    {
        $request->validate([
            'favoris' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $favoris = $user->favoris ? json_decode($user->favoris, true) : []; // Decode favoris from JSON if it exists

        if (!in_array($request->favoris, $favoris)) {
            $favoris[] = $request->favoris;
            $user->update(['favoris' => json_encode($favoris)]); // Save favoris as JSON
        }

        return response()->json(['message' => 'Favoris added successfully', 'favoris' => $favoris]);
    }

    public function addPoints(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $user->increment('points', $request->points);

        return response()->json(['message' => 'Points added successfully', 'total_points' => $user->points]);
    }
}
