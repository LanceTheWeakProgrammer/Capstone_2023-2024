<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageProfileAsUserController extends Controller
{
    public function show($id)
    {
        try {
            $user = User::where('id', $id)
                ->where('role', 'user')
                ->with([
                    'profile', 
                    'profile.bookings.technician.user',
                ])
                ->first();
    
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch user profile'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'string|max:255|nullable',
            'date_of_birth' => 'date|nullable',
            'phone_number' => 'string|max:20|nullable',
            'address' => 'string|max:255|nullable',
            'city' => 'string|max:255|nullable',
            'state' => 'string|max:255|nullable',
            'country' => 'string|max:255|nullable',
            'zip_code' => 'string|max:20|nullable',
            'profile_image' => 'string|max:255|nullable',
        ]);

        try {
            $user = User::where('id', $id)
                ->where('role', 'user')
                ->with('profile')
                ->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if (Auth::id() !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Update user profile with validated data
            $user->profile->update($request->only([
                'full_name',
                'date_of_birth',
                'phone_number',
                'address',
                'city',
                'state',
                'country',
                'zip_code',
                'profile_image',
            ]));

            return response()->json([
                'message' => 'User profile updated successfully',
                'user' => $user->load('profile')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user profile'], 500);
        }
    }
}
