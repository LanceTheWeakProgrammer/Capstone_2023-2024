<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        $user = $request->user();
    
        if ($user->role === 'technician' && is_null($user->logged_in_at)) {
            $request->validate([
                'new_password' => 'required|min:8|confirmed',
            ]);
    
            $user->update([
                'password' => Hash::make($request->new_password),
                'logged_in_at' => now(),
            ]);
    
            return response()->json([
                'message' => 'Password changed successfully. Welcome!',
            ], 200);
        }
    
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
    
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password does not match our records.',
            ], 403);
        }
    
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return response()->json([
            'message' => 'Password updated successfully.',
        ], 200);
    }
}
