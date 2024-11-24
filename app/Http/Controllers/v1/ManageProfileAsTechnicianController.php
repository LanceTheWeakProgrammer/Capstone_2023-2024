<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManageProfileAsTechnicianController extends Controller
{
    public function show($id)
    {
        try {
            $technician = User::where('id', $id)
                ->where('role', 'technician')
                ->with([
                    'technicianProfile', 
                    'technicianProfile.bookings.userProfile.user'
                ])
                ->first();
    
            if (!$technician) {
                return response()->json(['error' => 'Technician not found'], 404);
            }
    
            return response()->json(['technician' => $technician], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch technician profile'], 500);
        }
    }    

    public function update(Request $request, $id)
    {
        $request->validate([
            'bio' => 'string|nullable',
        ]);

        try {
            $technician = User::where('id', $id)
                ->where('role', 'technician')
                ->with('technicianProfile')
                ->first();

            if (!$technician) {
                return response()->json(['error' => 'Technician not found'], 404);
            }

            $technician->technicianProfile->update(['bio' => $request->bio]);

            return response()->json(['message' => 'Technician bio updated successfully', 'technician' => $technician], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update technician bio'], 500);
        }
    }
}
