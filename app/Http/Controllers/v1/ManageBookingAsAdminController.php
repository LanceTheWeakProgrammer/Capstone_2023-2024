<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Technician;
use App\Models\VehicleDetail;
use App\Models\Service;
use Exception;

class ManageBookingAsAdminController extends Controller
{
    public function getRegisteredBookings(Request $request)
    {
        try {
            $registeredBookings = Booking::with(['user', 'technician', 'vehicleDetail', 'services', 'attachments'])
                ->whereNotNull('user_id')
                ->orderBy('booking_date', 'desc')
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

    public function getGuestBookings(Request $request)
    {
        try {
            $guestBookings = Booking::with(['guest', 'technician', 'vehicleDetail', 'services', 'attachments'])
                ->whereNotNull('guest_id')
                ->orderBy('booking_date', 'desc')
                ->get();
    
            return response()->json([
                'message' => 'Guest bookings fetched successfully.',
                'bookings' => $guestBookings 
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching guest bookings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }    

    public function assignTechnicianToGuest(Request $request, $bookingId)
    {
        try {
            $validated = $request->validate([
                'technician_id' => 'required|exists:technician_profiles,id',
            ]);
    
            $booking = Booking::with(['vehicleDetail', 'services'])->findOrFail($bookingId);
            $technician = Technician::with(['vehicleTypes', 'services', 'bookings'])->findOrFail($request->technician_id);
    
            if (is_null($booking->guest_id)) {
                return response()->json(['message' => 'This booking does not belong to a guest.'], 400);
            }

            $bookingVehicleTypeIds = $booking->vehicleDetail->pluck('vehicle_type_id')->toArray();
            $technicianVehicleTypeIds = $technician->vehicleTypes->pluck('id')->toArray();
    
            if (!array_intersect($bookingVehicleTypeIds, $technicianVehicleTypeIds)) {
                return response()->json(['message' => 'Technician does not have the required vehicle mastery for this booking.'], 400);
            }

            $bookingServiceIds = $booking->services->pluck('id')->toArray();
            $technicianServiceIds = $technician->services->pluck('id')->toArray();
    
            if (!array_intersect($bookingServiceIds, $technicianServiceIds)) {
                return response()->json(['message' => 'Technician does not offer the required services for this booking.'], 400);
            }

            $existingBookings = Booking::where('technician_id', $technician->id)
                ->where('booking_date', $booking->booking_date)
                ->whereIn('status', ['Assigned', 'In Progress'])
                ->count();
    
            if ($existingBookings > 0) {
                return response()->json(['message' => 'Technician has a conflicting booking on the same date.'], 400);
            }

            $booking->technician_id = $technician->id;
            $booking->status = 'Assigned';
            $booking->save();
    
            return response()->json([
                'message' => 'Technician successfully assigned to the booking.',
                'booking' => $booking
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error assigning technician to the booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyBooking($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);

            if ($booking->status === 'Verified') {
                return response()->json([
                    'message' => 'Booking is already verified.'
                ], 400);
            }

            $booking->status = 'Verified';
            $booking->save();

            return response()->json([
                'message' => 'Booking verified successfully.',
                'booking' => $booking
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error verifying booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
