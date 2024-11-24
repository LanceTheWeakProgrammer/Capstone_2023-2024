<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Technician;
use App\Notifications\BookingNotification;
use App\Services\TechnicianService;
use Exception;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageBookingAsTechnicianController extends Controller
{
    protected $technicianService;

    public function __construct(TechnicianService $technicianService)
    {
        $this->technicianService = $technicianService;
    }

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

            $bookings = Booking::with([
                'userProfile.user',
                'technician',
                'vehicleDetail',
                'services',
                'attachments'
            ])
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

    public function toggleAvailability($technicianId, $status)
    {
        try {
            $technician = Technician::findOrFail($technicianId);

            $inactiveStatuses = ['Cancelled', 'Declined', 'Completed', 'No Show'];
            $activeBookingsCount = Booking::where('technician_id', $technicianId)
                ->whereNotIn('status', $inactiveStatuses)
                ->count();

            if ($activeBookingsCount >= 5) {
                return response()->json([
                    'message' => 'Unable to set availability. Please finish your current bookings before becoming available.'
                ], 400);
            }

            $technician->avail_status = $status === 'active';
            $technician->save();

            $technician->user->update([
                'status' => $technician->avail_status ? 'active' : 'busy'
            ]);

            return response()->json([
                'message' => 'Technician availability updated successfully',
                'status' => $technician->avail_status ? 'active' : 'busy'
            ], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while updating technician availability', 'message' => $e->getMessage()], 500);
        }
    }

    public function reschedule(Request $request, $bookingId)
    {
        try {
            $validated = $request->validate(['new_booking_date' => 'required|date']);
    
            $user = auth()->user();
            $technicianProfile = $user->technicianProfile;
    
            if (!$technicianProfile) {
                return response()->json(['error' => 'Technician profile not found for this user.'], 404);
            }
    
            $booking = Booking::with(['userProfile.user', 'vehicleDetail', 'services', 'attachments'])
                ->where('id', $bookingId)
                ->where('technician_id', $technicianProfile->id)
                ->firstOrFail();
    
            if ($booking->status !== 'Approved') {
                return response()->json(['message' => 'Only approved bookings can be rescheduled.'], 400);
            }
    
            $newBookingDate = new DateTime($validated['new_booking_date']);
            $today = new DateTime('today');
            $maxBookingDate = (new DateTime())->modify('+12 months');
    
            if ($newBookingDate <= $today) {
                return response()->json(['message' => 'The new booking date must be after today.'], 400);
            }
    
            if ($newBookingDate > $maxBookingDate) {
                return response()->json(['message' => 'The new booking date must be within 12 months from today.'], 400);
            }
    
            $conflictingBookings = Booking::where('technician_id', $technicianProfile->id)
                ->where('booking_date', $validated['new_booking_date'])
                ->whereNotIn('status', ['Cancelled', 'Declined', 'Completed', 'No Show'])
                ->count();
    
            if ($conflictingBookings > 0) {
                return response()->json(['message' => 'Technician is not available on the selected date.'], 400);
            }
    
            $booking->update(['booking_date' => $validated['new_booking_date'], 'status' => 'Rescheduled']);
    
            $message = "Your booking has been rescheduled to " . $newBookingDate->format('F j, Y') . ".";
            $booking->userProfile->user->notify(new BookingNotification($booking, $message, 'rescheduled'));
    
            return response()->json(['message' => 'Booking rescheduled successfully', 'booking' => $booking], 200);
    
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while rescheduling the booking.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
}
