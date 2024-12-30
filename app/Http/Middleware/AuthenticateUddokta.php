<?php

namespace App\Http\Middleware;

use App\Models\TokenBlacklist; // Import your TokenBlacklist model
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateUddokta
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the Bearer token from the Authorization header
        $token = $request->bearerToken();

        // Check if the token is blacklisted
        if ($token && TokenBlacklist::where('token', $token)->exists()) {
            return response()->json([], 401);
        }

        // Check if the Uddokta is authenticated
        if (!Auth::guard('uddokta')->check()) {
            return response()->json([], 401);
        }

        return $next($request);
    }
}
