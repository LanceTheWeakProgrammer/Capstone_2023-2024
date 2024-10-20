<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|string|size:6',  
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid verification code.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('verification_code', $request->verification_code)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Verification code not found.',
            ], 404);
        }

        if ($user->is_verified) {
            return response()->json([
                'message' => 'User is already verified.',
            ], 400);
        }

        $user->is_verified = true;
        $user->verification_code = null;  
        $user->email_verified_at = now();
        $user->save();

        Auth::login($user);

        return response()->json([
            'message' => 'Email successfully verified and user logged in!',
        ], 200);
    }
}
