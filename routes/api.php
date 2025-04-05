<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\AdminController;

// Authentication Routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// User Profile Routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});
Route::middleware('auth:sanctum')->patch('/user', [UserController::class, 'update']);

// Profile Management Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/favoris', [ProfileController::class, 'addFavoris']);
    Route::post('/profile/points', [ProfileController::class, 'addPoints']);
});

// Facebook OAuth Routes
Route::get('auth/facebook', [FacebookAuthController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [FacebookAuthController::class, 'handleFacebookCallback']);

// Routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'home']);
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/profile', [AdminController::class, 'getUser']);
    Route::put('/admin/profile', [AdminController::class, 'updateProfile']);
});


// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::patch('/user', [UserController::class, 'update']);
    Route::post('/upload-photo', [UserController::class, 'uploadPhoto']);
});
