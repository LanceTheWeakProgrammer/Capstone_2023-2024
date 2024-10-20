<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index() 
    {
        try {
            $service = Service::all();

            return response()->json([
                'success' => true,
                'data' => $service
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request) 
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:svg|max:2048',
            'fee' => 'required|numeric|min:0|max:9999999.99',
            'description' => 'required|string'
        ]);
    
        try {
            $randomNumber = mt_rand(1000000, 9999999);
    
            $iconName = 'ICO_' . $randomNumber . '.' . $request->file('icon')->getClientOriginalExtension();
    
            $iconPath = $request->file('icon')->storeAs('images/services', $iconName, 'public');

            $validate['fee'] = number_format($validate['fee'], 2, '.', '');
    
            $service = Service::create([
                'name' => $validate['name'],
                'icon' => $iconPath,
                'fee' => $validate['fee'],
                'description' => $validate['description']
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Service data added successfully.',
                'data' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add service data: '  . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);

            if($service->icon) {
                Storage::disk('public')->delete($service->icon);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service data deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service data: ' . $e->getMessage()
            ], 500);
        }
    }
}
