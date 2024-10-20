<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Exception;

class ManageBookingAsTechnicianController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $technicianProfile = $user->technicianProfile;

            if (!$technicianProfile) {
                return response()->json([
                    'error' => 'Technician profile not found for this user.'
                ], 404);
            }

            $technicianId = $technicianProfile->id; 

            $bookings = Booking::with('user', 'vehicleDetail', 'services')
                                ->where('technician_id', $technicianId) 
                                ->get();

            return response()->json([
                'message' => 'Bookings retrieved successfully',
                'data' => $bookings
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while fetching bookings',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approveBooking($bookingId)
    {
        try {
            $user = auth()->user();
            $technicianProfile = $user->technicianProfile;

            if (!$technicianProfile) {
                return response()->json([
                    'error' => 'Technician profile not found for this user.'
                ], 404);
            }

            $booking = Booking::with('user', 'vehicleDetail', 'services')
                            ->where('technician_id', $technicianProfile->id)
                            ->where('id', $bookingId)
                            ->first();

            if (!$booking) {
                return response()->json([
                    'error' => 'Booking not found.'
                ], 404);
            }

            $booking->status = 'Approved';
            $booking->save();

            return response()->json([
                'message' => 'Booking approved successfully',
                'booking' => $booking
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while approving the booking',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function declineBooking($bookingId)
    {
        try {
            $user = auth()->user();
            $technicianProfile = $user->technicianProfile;

            if (!$technicianProfile) {
                return response()->json([
                    'error' => 'Technician profile not found for this user.'
                ], 404);
            }

            $booking = Booking::with('user', 'vehicleDetail', 'services')
                            ->where('technician_id', $technicianProfile->id)
                            ->where('id', $bookingId)
                            ->first();

            if (!$booking) {
                return response()->json([
                    'error' => 'Booking not found.'
                ], 404);
            }

            $booking->status = 'Declined';
            $booking->save();

            return response()->json([
                'message' => 'Booking declined successfully',
                'booking' => $booking
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while declining the booking',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

