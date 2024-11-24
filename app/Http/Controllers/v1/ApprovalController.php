<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingNotification;
use App\Services\TechnicianService;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $technicianService;

    public function __construct(TechnicianService $technicianService)
    {
        $this->technicianService = $technicianService;
    }

    public function approveBooking($bookingId)
    {
        try {
            $booking = $this->retrieve($bookingId);

            if (!in_array($booking->status, ['Pending', 'Reschedule Requested'])) {
                return response()->json(['message' => 'This booking cannot be approved as it is not in a valid state.'], 400);
            }

            $booking->status = 'Approved';
            $booking->save();

            $this->technicianService->manageQuota($booking->technician_id);

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Your booking has been approved.', 'approved'));

            return response()->json(['message' => 'Booking approved successfully', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong while approving the booking', 'message' => $e->getMessage()], 500);
        }
    }

    public function declineBooking($bookingId)
    {
        try {
            $booking = $this->retrieve($bookingId);

            if (!in_array($booking->status, ['Pending', 'Reschedule Requested'])) {
                return response()->json(['message' => 'This booking cannot be declined as it is not in a valid state.'], 400);
            }

            $booking->status = 'Declined';
            $booking->save();

            $this->technicianService->manageQuota($booking->technician_id);

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Your booking has been declined.', 'declined'));

            return response()->json(['message' => 'Booking declined successfully', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong while declining the booking', 'message' => $e->getMessage()], 500);
        }
    }

    public function approveCancelRequest($bookingId)
    {
        try {
            $booking = $this->retrieve($bookingId);

            if ($booking->status !== 'Cancellation Requested') {
                return response()->json(['message' => 'This booking does not have a cancellation request.'], 400);
            }

            $booking->status = 'Cancelled';
            $booking->save();

            $this->technicianService->manageQuota($booking->technician_id);

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Your cancellation request has been approved.', 'cancelled'));

            return response()->json(['message' => 'Booking cancelled successfully', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong while approving the cancellation request', 'message' => $e->getMessage()], 500);
        }
    }

    public function declineCancelRequest(Request $request, $bookingId)
    {
        try {
            $validated = $request->validate(['decline_reason' => 'nullable|string|min:10|max:500']);
            $booking = $this->retrieve($bookingId);

            if ($booking->status !== 'Cancellation Requested') {
                return response()->json(['message' => 'This booking does not have a cancellation request.'], 400);
            }

            $booking->status = 'Approved';
            $booking->save();

            $booking->justifications()->create([
                'type' => 'Declined Cancel Request',
                'justification' => $validated['decline_reason'] ?? 'No reason provided'
            ]);

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Your cancellation request has been declined.', 'decline_cancel'));

            return response()->json(['message' => 'Cancellation request declined successfully', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong while declining the cancellation request', 'message' => $e->getMessage()], 500);
        }
    }

    public function approveRescheduleRequest($bookingId)
    {
        try {
            $booking = $this->retrieve($bookingId);

            if ($booking->status !== 'Reschedule Requested') {
                return response()->json(['message' => 'This booking does not have a reschedule request.'], 400);
            }

            $rescheduleRequest = $booking->justifications()
                ->where('type', 'Request Reschedule')
                ->latest()
                ->first();

            if (!$rescheduleRequest || !$rescheduleRequest->requested_date) {
                return response()->json(['message' => 'Requested date not found for reschedule request.'], 400);
            }

            $booking->booking_date = Carbon::parse($rescheduleRequest->requested_date);
            $booking->status = 'Rescheduled';
            $booking->save();

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Your reschedule request has been approved.', 'rescheduled'));

            return response()->json(['message' => 'Reschedule request approved successfully', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong while approving the reschedule request', 'message' => $e->getMessage()], 500);
        }
    }

    public function declineRescheduleRequest(Request $request, $bookingId)
    {
        try {
            $validated = $request->validate(['decline_reason' => 'required|string|min:10|max:500']);
            $booking = $this->retrieve($bookingId);

            if ($booking->status !== 'Reschedule Requested') {
                return response()->json(['message' => 'This booking does not have a reschedule request.'], 400);
            }

            $booking->status = 'Approved';
            $booking->save();

            $booking->justifications()->create([
                'type' => 'Declined Reschedule Request',
                'justification' => $validated['decline_reason']
            ]);

            $booking->userProfile->user->notify(new BookingNotification($booking, 'Your reschedule request has been declined.', 'decline_reschedule'));

            return response()->json(['message' => 'Reschedule request declined successfully', 'booking' => $booking], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong while declining the reschedule request', 'message' => $e->getMessage()], 500);
        }
    }

    private function retrieve($bookingId)
    {
        $user = auth()->user();
        $technicianProfile = $user->technicianProfile;

        if (!$technicianProfile) {
            throw new Exception('Technician profile not found for this user.');
        }

        return Booking::with('userProfile.user')->where('technician_id', $technicianProfile->id)->findOrFail($bookingId);
    }
}
