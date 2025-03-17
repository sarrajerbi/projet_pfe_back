<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure the user is authenticated via Sanctum
        if (Auth::guard('sanctum')->check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // If the user is not authenticated or not an admin, return a 403 Forbidden response
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure the user is authenticated via Sanctum
        if (Auth::guard('sanctum')->check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // If the user is not authenticated or not an admin, return a 403 Forbidden response
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
