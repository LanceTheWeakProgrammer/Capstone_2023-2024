<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\TechnicianAccountDetailsMail;

class ManageTechnicianAsAdminController extends Controller
{
    public function index()
    {
        try {
            $technicians = Technician::with(['vehicleTypes', 'services', 'user', 'ratings'])
                ->where('is_removed', false)
                ->get();
    
            return response()->json([
                'success' => true,
                'data' => $technicians
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve technicians: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'date_of_birth' => 'required|date',
            'phone_number' => 'required|string|max:15',
            'year_experience' => 'required|string|max:4',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'vehicle_type_ids' => 'required|string',  
            'service_ids' => 'required|string',
        ]);

        $service_ids = json_decode($request->service_ids);
        $vehicle_type_ids = json_decode($request->vehicle_type_ids); 

        try {
            do {
                $account_number = '10' . mt_rand(1000000, 9999999);
            } while (User::where('account_number', $account_number)->exists());

            $user = User::create([
                'account_number' => $account_number,
                'email' => $validate['email'],
                'password' => Hash::make('P@ssword!123'),
                'role' => 'technician',
                'is_active' => true,
                'status' => 'active',
            ]);

            $random_number = mt_rand(1000000, 9999999);
            $image_name = 'IMG_' . $random_number . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $image_path = $request->file('profile_image')->storeAs('images/technicians', $image_name, 'public');

            $technician = Technician::create([
                'user_id' => $user->id,
                'full_name' => $validate['name'],
                'date_of_birth' => $validate['date_of_birth'],
                'phone_number' => $validate['phone_number'],
                'year_experience' => $validate['year_experience'],
                'profile_image' => $image_path,
                'avail_status' => 1,
                'is_removed' => false,
            ]);

            $technician->vehicleTypes()->attach($vehicle_type_ids);
            $technician->services()->attach($service_ids);

            Mail::to($validate['email'])->queue(new TechnicianAccountDetailsMail($account_number, 'P@ssword!123'));

            return response()->json([
                'success' => true,
                'message' => 'Technician created successfully, and account details sent via email.',
                'data' => $technician
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create technician. Please try again later.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $technician = Technician::with(['vehicleTypes', 'services', 'user', 'ratings.user'])->findOrFail($id);
    
            $technician->vehicle_type_ids = $technician->vehicleTypes->pluck('id');
            $technician->service_ids = $technician->services->pluck('id');
    
            return response()->json([
                'success' => true,
                'data' => $technician
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve technician: ' . $e->getMessage()
            ], 500);
        }
    }      

    public function update(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|exists:technician_profiles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user_id,
            'date_of_birth' => 'required|date',
            'phone_number' => 'required|string|max:15',
            'year_experience' => 'required|string|max:4',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'vehicle_type_ids' => 'required|string',  
            'service_ids' => 'required|string',
        ]);

        $service_ids = json_decode($request->service_ids);
        $vehicle_type_ids = json_decode($request->vehicle_type_ids); 

        try {
            $technician = Technician::findOrFail($validate['id']);

            $technician->user()->update([
                'email' => $validate['email']
            ]);

            if ($request->hasFile('profile_image')) {
                if ($technician->profile_image) {
                    Storage::disk('public')->delete($technician->profile_image);
                }
                $random_number = mt_rand(1000000, 9999999);
                $image_name = 'IMG_' . $random_number . '.' . $request->file('profile_image')->getClientOriginalExtension();
                $image_path = $request->file('profile_image')->storeAs('images/technicians', $image_name, 'public');
                $technician->profile_image = $image_path;
            }

            $technician->update([
                'full_name' => $validate['name'],
                'date_of_birth' => $validate['date_of_birth'],
                'phone_number' => $validate['phone_number'],
                'year_experience' => $validate['year_experience'],
                'profile_image' => isset($image_path) ? $image_path : $technician->profile_image,
            ]);

            $technician->services()->sync($service_ids);
            $technician->vehicleTypes()->sync($vehicle_type_ids);

            return response()->json([
                'success' => true,
                'message' => 'Technician updated successfully.',
                'data' => $technician
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to update technician:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update technician. Please try again later.'
            ], 500);
        }
    }

    public function toggle($id)
    {
        try {
            $technician = Technician::with('user')->findOrFail($id);

            $technician->user->is_active = !$technician->user->is_active;
            $technician->avail_status = $technician->user->is_active ? 1 : 2;

            $technician->user->save();
            $technician->save();

            return response()->json([
                'success' => true,
                'message' => 'Technician status updated successfully.',
                'data' => $technician
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update technician status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function remove($id)
    {
        try {
            $technician = Technician::with('user')->findOrFail($id);

            if ($technician->user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove an active technician. Please deactivate the technician first.'
                ], 400);
            }

            $technician->is_removed = true;
            $technician->save();

            return response()->json([
                'success' => true,
                'message' => 'Technician removed successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove technician: ' . $e->getMessage()
            ], 500);
        }
    }
}
