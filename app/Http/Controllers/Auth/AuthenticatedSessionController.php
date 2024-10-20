<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $role = $user->role;

        $isFirstLogin = is_null($user->logged_in_at);

        if ($isFirstLogin) {
            $user->update(['logged_in_at' => Carbon::now()]);
        }

        // Create token for the session
        $token = $user->createToken('API Token for ' . $role)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'profile_id' => $user->profile()->pluck('id')->first(), 
                'is_first_login' => $isFirstLogin, 
            ],
            'role' => $role,
            'token' => $token,
        ]);
    }

    /**
     * Destroy an authenticated session (Logout).
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
