<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\UserProfile;
use App\Models\Technician;
use App\Models\Payment;
use App\Mail\SendSuccessMail;
use App\Services\TwilioService;
use App\Services\TechnicianService;
use Illuminate\Support\Facades\Mail;
use App\Notifications\BookingNotification;
use Exception;
use DateTime; 

class UserBookingController extends Controller
{
    protected $twilioService;
    protected $technicianService;

    public function __construct(TwilioService $twilioService, TechnicianService $technicianService)
    {
        $this->twilioService = $twilioService;
        $this->technicianService = $technicianService;
    }

    public function store(Request $request)
    {
        try {
            $userProfile = auth()->user()->profile;
    
            $validated = $request->validate([
                'technician_id' => 'required|exists:technician_profiles,id',
                'booking_date' => 'required|date|after:today',
                'vehicle_id' => 'required|exists:vehicle_details,id',
                'service_ids' => 'required|string',
                'total_fee' => 'required|numeric',
                'additional_info' => 'nullable|string',
                'attachments.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
    
            $maxBookingDate = now()->addMonths(12); 

            if (new DateTime($validated['booking_date']) > $maxBookingDate) {
                return response()->json([
                    'message' => 'The booking date must be within 12 months from today.'
                ], 400);
            }

            $service_ids = json_decode($request->service_ids, true);

            if (count($service_ids) === 0) {
                return response()->json(['message' => 'At least one valid service is required.'], 400);
            }

            $booking = Booking::create([
                'user_id' => $userProfile->id,
                'technician_id' => $request->technician_id,
                'vehicle_detail_id' => $request->vehicle_id,
                'booking_date' => $request->booking_date,
                'status' => 'Pending',
                'total_fee' => $request->total_fee,
                'additional_info' => $request->additional_info,
            ]);

            foreach ($service_ids as $service_id) {
                $serviceDetails = Service::findOrFail($service_id);
                $booking->services()->attach($serviceDetails, ['service_fee' => 100]);
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $randomNumber = mt_rand(1000000, 9999999);
                    $imageName = 'IMG_' . $randomNumber . '.' . $attachment->getClientOriginalExtension();
                    $path = $attachment->storeAs('images/attachments', $imageName, 'public');
                    $booking->attachments()->create(['image' => $path]);
                }
            }

            Payment::create([
                'user_id' => $userProfile->id,
                'booking_id' => $booking->id,
                'amount' => $request->total_fee,
                'currency' => 'PHP',
                'status' => 'Not Paid', 
                'payment_method' => null,
                'transaction_id' => null,
                'payment_date' => null,
                'notes' => 'Payment is pending approval and confirmation.',
            ]);
    
            $this->technicianService->manageQuota($request->technician_id);

            $userProfile->user->notify(new BookingNotification($booking, "Booking created successfully, wait for approval.", 'created'));
            $technician = Technician::findOrFail($request->technician_id);
            $technician->user->notify(new BookingNotification($booking, "Someone has booked you.", 'created'));

            // Send confirmation email to user
            // Mail::to(auth()->user()->email)->queue(new SendSuccessMail($booking));

            // Send SMS confirmation to user
            // $this->twilioService->sendSMS($userProfile->phone_number, "Your booking has been created successfully.");
    
            return response()->json([
                'message' => 'Booking created successfully for registered user',
                'booking' => $booking,
            ], 201);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the booking for registered user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function index()
    {
        try {
            $user = auth()->user();
 
            $bookings = Booking::with(['vehicleDetail', 'services', 'technician.ratings', 'payments'])
                ->where('user_id', $user->profile->id) 
                ->get();
    
            return response()->json([
                'message' => 'Bookings retrieved successfully',
                'bookings' => $bookings, 
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the bookings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $booking = Booking::with(['vehicleDetail.vehicleType', 'services', 'technician.user', 'payments', 'attachments', 'userProfile.user'])
                ->where('id', $id)
                ->where('user_id', auth()->user()->profile->id)
                ->firstOrFail();

            return response()->json([
                'message' => 'Booking retrieved successfully',
                'booking' => $booking,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the booking.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function requestCancel(Request $request, $bookingId)
    {
        try {
            $booking = Booking::where('id', $bookingId)
                            ->where('user_id', auth()->user()->profile->id) 
                            ->firstOrFail();

            if ($booking->status === 'Completed' || $booking->status === 'In Progress') {
                return response()->json([
                    'message' => 'This booking cannot be canceled as it is already in progress or completed.'
                ], 403);
            }

            $booking->update(['status' => 'Cancellation Requested']);

            return response()->json([
                'message' => 'Your cancellation request has been submitted.',
                'booking' => $booking
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while requesting cancellation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function requestReschedule(Request $request, $bookingId)
    {
        try {
            $booking = Booking::where('id', $bookingId)
                              ->where('user_id', auth()->user()->profile->id)
                              ->firstOrFail();
    
            if (!in_array($booking->status, ['Approved', 'In Progress', 'Rescheduled'])) {
                return response()->json([
                    'message' => 'Only approved, in progress, or rescheduled bookings can be rescheduled.'
                ], 403);
            }

            if ($booking->status === 'Reschedule Requested') {
                return response()->json([
                    'message' => 'A reschedule request has already been submitted. Please wait for it to be processed.'
                ], 400);
            }
    
            $validated = $request->validate([
                'requested_date' => 'required|date|after:today'
            ]);

            $maxRescheduleDate = now()->addMonths(12); 

            if (new DateTime($validated['requested_date']) > $maxRescheduleDate) {
                return response()->json([
                    'message' => 'The requested reschedule date must be within 12 months from today.'
                ], 400);
            }

            $booking->justifications()->create([
                'type' => 'Request Reschedule',
                'requested_date' => $validated['requested_date'],
                'justification' => $request->justification ?? null,
            ]);

            $booking->status = 'Reschedule Requested';
            $booking->save();
    
            $technician = $booking->technician;
            if ($technician && $technician->user) {
                $technician->user->notify(new BookingNotification(
                    $booking,
                    "A reschedule request has been submitted by the user.",
                    'reschedule_requested'
                ));
            }
    
            return response()->json([
                'message' => 'Reschedule request submitted successfully.',
                'requested_date' => $validated['requested_date']
            ], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while submitting the reschedule request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
