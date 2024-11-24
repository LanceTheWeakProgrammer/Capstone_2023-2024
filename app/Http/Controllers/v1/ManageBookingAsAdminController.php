<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Exception;

class ManageBookingAsAdminController extends Controller
{
    public function getRegisteredBookings(Request $request)
    {
        try {
            $registeredBookings = Booking::with(['user', 'technician', 'vehicleDetail', 'services', 'attachments', 'payments'])
                ->whereNotNull('user_id') 
                ->get();

            return response()->json([
                'message' => 'Registered bookings fetched successfully.',
                'data' => $registeredBookings
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching registered bookings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getActiveBookings(Request $request)
    {
        try {
            $activeStatuses = ['Approved', 'Rescheduled', 'Reschedule Requested', 'In Progress'];

            $activeBookings = Booking::with(['user', 'technician', 'vehicleDetail', 'services', 'attachments', 'payments'])
                ->whereIn('status', $activeStatuses) 
                ->orderBy('booking_date', 'desc')
                ->get();

            return response()->json([
                'message' => 'Active bookings fetched successfully.',
                'data' => $activeBookings
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching active bookings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
