<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Shelfed

// class BookingCreated implements ShouldBroadcast
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $booking;
//     public $message;
//     public $technicianMessage;

//     public function __construct(Booking $booking, $message, $technicianMessage)
//     {
//         $this->booking = $booking;
//         $this->message = $message;
//         $this->technicianMessage = $technicianMessage;
//     }

//     public function broadcastOn()
//     {
//         $userId = $this->booking->userProfile->user->id;
//         $technicianId = $this->booking->technician->user->id;

//         return [
//             new PrivateChannel('user.' . $userId),
//             new PrivateChannel('technician.' . $technicianId),
//         ];
//     }

//     public function broadcastWith()
//     {
//         $data = [
//             'booking_id' => $this->booking->id,
//             'message' => $this->message,
//             'technician_message' => $this->technicianMessage,
//             'status' => $this->booking->status,
//             'booking_date' => $this->booking->booking_date,
//         ];
//         \Log::info('Broadcasting BookingCreated with data:', $data);  
//         return $data;
//     }
// }
