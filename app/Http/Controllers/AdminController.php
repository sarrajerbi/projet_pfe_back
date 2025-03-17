<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function getUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        return response()->json(['user' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validation des données
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $admin->id,
            'new_password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Mise à jour des champs
        if ($request->filled('name')) {
            $admin->name = $validatedData['name'];
        }

        if ($request->filled('lname')) {
            $admin->lname = $validatedData['lname'];
        }

        if ($request->filled('email')) {
            $admin->email = $validatedData['email'];
        }

        if ($request->filled('new_password')) {
            $admin->password = Hash::make($validatedData['new_password']);
        }

        // Gestion de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                Storage::disk('public')->delete($admin->photo);
            }

            // Stocker la nouvelle photo
            $photoPath = $request->file('photo')->store('photos', 'public');
            $admin->photo = $photoPath;
        }

        // Sauvegarde des modifications
        $admin->save();

        // Retourner une réponse avec les données mises à jour
        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'name' => $admin->name,
                'lname' => $admin->lname,
                'email' => $admin->email,
                'photo' => $admin->photo ? asset('storage/' . $admin->photo) : null, // Renvoi de la photo mise à jour
            ],
        ]);
    }
}
