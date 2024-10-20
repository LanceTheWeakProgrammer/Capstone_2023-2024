<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Service;
use App\Models\UserProfile;
use App\Models\VehicleDetail;
use App\Models\Technician;
use App\Mail\SendGuestTokenMail;
use App\Mail\SendSuccessMail;
use App\Mail\BookingTransferredMail;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Mail;
use Exception;

class UserBookingController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function storeForRegisteredUser(Request $request)
    {
        try {
            $userProfile = auth()->user()->profile;

            $validated = $request->validate([
                'technician_id' => 'required|exists:technician_profiles,id',
                'booking_date' => 'required|date',
                'vehicle_id' => 'required|exists:vehicle_details,id',
                'service_ids' => 'required|string',
                'total_fee' => 'required|numeric',
                'additional_info' => 'nullable|string',
                'attachments.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
    
            $service_ids = json_decode($request->service_ids, true);
    
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

            Mail::to(auth()->user()->email)->queue(new SendSuccessMail($booking));

            return response()->json([
                'message' => 'Booking created successfully for registered user',
                'booking' => $booking
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the booking for registered user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeForGuestUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'guest_name' => 'required|string',
                'guest_email' => 'required|email',
                'guest_phone' => 'required|string',
                'booking_date' => 'required|date',
                'vehicle_id' => 'required|exists:vehicle_details,id', 
                'service_ids' => 'required|string',
                'total_fee' => 'required|numeric',
                'additional_info' => 'nullable|string',
            ]);
    
            $service_ids = json_decode($request->service_ids, true);
            $booking_date = $request->booking_date;

            $matchingTechnicians = Technician::whereHas('vehicleTypes', function($query) use ($request) {
                    $query->where('vehicle_types.id', $request->vehicle_id);
                })
                ->whereHas('services', function($query) use ($service_ids) {
                    $query->whereIn('service.id', $service_ids); 
                })
                ->whereDoesntHave('bookings', function($query) use ($booking_date) {
                    $query->where('booking_date', $booking_date);
                })
                ->where('avail_status', true)
                ->first();

            $guest = Guest::create([
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'guest_token' => $this->generateGuestToken(),
            ]);
    
            Mail::to($guest->guest_email)->queue(new SendGuestTokenMail($guest, $guest->guest_token));

            $booking = Booking::create([
                'guest_id' => $guest->id,
                'technician_id' => $matchingTechnicians ? $matchingTechnicians->id : null,
                'vehicle_detail_id' => $request->vehicle_id,
                'booking_date' => $request->booking_date,
                'status' => 'Pending',
                'total_fee' => $request->total_fee,
                'additional_info' => $request->additional_info,
            ]);

            foreach ($service_ids as $service_id) {
                $serviceDetails = Service::findOrFail($service_id);
                $booking->services()->attach($serviceDetails, ['service_fee' => 0]);
            }

            // if (!empty($guest->guest_phone)) {
            //     $this->twilioService->sendSMS($guest->guest_phone, "Your booking has been successfully created. Your guest token is {$guest->guest_token}.");
            // }

            if (!$matchingTechnicians) {
                return response()->json([
                    'message' => 'Booking created successfully. No technician has been assigned to your booking yet. We are actively working on assigning one and will notify you as soon as a technician is available.',
                    'booking' => $booking,
                ], 201);
            }
    
            return response()->json([
                'message' => 'Booking created successfully for guest user with assigned technician.',
                'booking' => $booking,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the booking for guest user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
     
    public function index()
    {
        try {
            $bookings = Booking::with(['vehicleDetail', 'services', 'guest', 'user'])
                ->orderBy('booking_date', 'desc')
                ->get();

            return response()->json([
                'message' => 'All bookings retrieved successfully',
                'bookings' => $bookings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the bookings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function transfer(Request $request)
    {
        try {
            $validated = $request->validate([
                'guest_token' => 'required|string|exists:guests,guest_token',
                'user_id' => 'required|exists:user_profiles,id',
            ]);
    
            $guest = Guest::where('guest_token', $request->guest_token)->firstOrFail();
            $relatedGuests = Guest::where(function($query) use ($guest) {
                $query->where('guest_email', $guest->guest_email)
                      ->orWhere('guest_phone', $guest->guest_phone);
            })->get();

            $bookings = Booking::whereIn('guest_id', $relatedGuests->pluck('id'))->get();

            $bookings->each(function ($booking) use ($request) {
                $booking->update(['user_id' => $request->user_id, 'guest_id' => null]);
            });

            if ($guest->guest_email) {
                Mail::to($guest->guest_email)->queue(new BookingTransferredMail($guest));
            }

            $relatedGuests->each->delete();
    
            return response()->json([
                'message' => 'Guest bookings transferred successfully.',
                'bookings' => $bookings,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error transferring bookings.'], 500);
        }
    }
    
    public function show()
    {
        try {
            $user = auth()->user();
 
            $bookings = Booking::with(['vehicleDetail', 'services', 'guest', 'technician'])
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
    
    private function generateGuestToken()
    {
        try {
            do {
                $letters1 = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                $letters2 = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                $digits = mt_rand(1000, 9999);
                $token = "{$letters1}-{$letters2}-{$digits}";
            } while (Guest::where('guest_token', $token)->exists());

            return $token;
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while generating the guest token.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
