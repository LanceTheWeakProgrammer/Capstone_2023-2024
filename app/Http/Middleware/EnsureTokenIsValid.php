<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = $request->user();

            if (!$user->tokens()->find($user->currentAccessToken()->id)) {
                return response()->json(['message' => 'Token is invalid or expired'], 401);
            }
        } else {
            return response()->json(['message' => 'Unauthorized, please login'], 401);
        }
        
        return $next($request);
    }
}
