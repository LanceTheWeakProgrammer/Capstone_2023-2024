<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use App\Models\VehicleDetail;

class VehicleController extends Controller
{
    public function getVehicleTypes() 
    {
        try {
            $vehicleTypes = VehicleType::all();

            return response()->json([
                'success' => true,
                'data' => $vehicleTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle types: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVehicleTypeById($id)
    {
        try {
            $vehicleType = VehicleType::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $vehicleType
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle type: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVehicleDetails($typeId)
    {
        try {
            $vehicleDetails = VehicleDetail::where('vehicle_type_id', $typeId)
                ->with('vehicleType')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $vehicleDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request) 
    {
        $validate = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
        ]);
    
        try {
            $vehicleType = VehicleType::firstOrCreate(['type' => $validate['vehicle_type']]);

            $vehicleDetail = VehicleDetail::create([
                'make' => $validate['make'],
                'model' => $validate['model'],
                'vehicle_type_id' => $vehicleType->id, 
            ]);

            $vehicleDetail->load('vehicleType');

            return response()->json([
                'success' => true,
                'message' => 'Vehicle details and type added successfully.',
                'data' => $vehicleDetail
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add vehicle details: ' . $e->getMessage()
            ], 500);
        }
    }
    

    public function index() 
    {
        try {
            $vehicleData = VehicleDetail::with('vehicleType')->get();

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id) 
    {
        try {
            $vehicleDetail = VehicleDetail::findOrFail($id);
            $vehicleDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle detail deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete vehicle detail: ' . $e->getMessage()
            ], 500);
        }
    }
}
