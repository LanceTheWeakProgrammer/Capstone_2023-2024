<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingNotification;
use App\Services\TechnicianService;
use Exception;
use Illuminate\Http\Request;

class BookingStatusController extends Controller
{
    protected $technicianService;

    public function __construct(TechnicianService $technicianService)
    {
        $this->technicianService = $technicianService;
    }

    public function inProgress($bookingId)
    {
        try {
            $booking = $this->retrieve($bookingId);

            if (!in_array($booking->status, ['Approved', 'Rescheduled'])) {
                return response()->json(['message' => 'Only approved or rescheduled bookings can be marked as In Progress.'], 400);
            }

            $booking->status = 'In Progress';
            $booking->save();

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Service is in progress.', 'in_progress'));

            return response()->json(['message' => 'Booking marked as In Progress', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the booking status', 'message' => $e->getMessage()], 500);
        }
    }

    public function completed($bookingId)
    {
        try {
            $booking = $this->retrieve($bookingId);

            if (!in_array($booking->status, ['In Progress', 'Rescheduled'])) {
                return response()->json(['message' => 'Only bookings in progress or rescheduled can be marked as Completed.'], 400);
            }

            $booking->status = 'Completed';
            $booking->save();

            $this->technicianService->manageQuota($booking->technician_id);

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Service is completed. Thank you for choosing our service.', 'completed'));

            return response()->json(['message' => 'Booking marked as Completed', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the booking status', 'message' => $e->getMessage()], 500);
        }
    }

    public function noShow($bookingId)
    {
        try {
            $booking = $this->retrieve($bookingId);

            if (!in_array($booking->status, ['Approved', 'In Progress'])) {
                return response()->json(['message' => 'Only approved or in-progress bookings can be marked as No Show.'], 400);
            }

            $booking->status = 'No Show';
            $booking->save();

            $this->technicianService->manageQuota($booking->technician_id);

            $booking->userProfile->user->notify(new BookingNotification($booking, 'The technician marked your booking as No Show.', 'no_show'));

            return response()->json(['message' => 'Booking marked as No Show', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the booking status', 'message' => $e->getMessage()], 500);
        }
    }

    private function retrieve($bookingId)
    {
        $user = auth()->user();
        $technicianProfile = $user->technicianProfile;

        if (!$technicianProfile) {
            throw new Exception('Technician profile not found for this user.');
        }

        return Booking::with('userProfile.user')
                     ->where('technician_id', $technicianProfile->id)
                     ->findOrFail($bookingId);
    }
}
