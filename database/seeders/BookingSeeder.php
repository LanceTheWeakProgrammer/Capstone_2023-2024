<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{

    public function run(): void
    {
        $technicianId = 1; 
        $vehicleDetailId = 1; 
        $userId = 1;

        for ($i = 1; $i <= 4; $i++) {
            $booking = Booking::create([
                'user_id' => $userId,
                'technician_id' => $technicianId,
                'vehicle_detail_id' => $vehicleDetailId,
                'booking_date' => now()->addDays($i), 
                'total_fee' => 150.00 * $i, 
                'status' => 'Pending',
                'additional_info' => 'Test booking ' . $i,
            ]);

            DB::table('services_selected')->insert([
                [
                    'booking_id' => $booking->id,
                    'service_id' => 1, 
                    'service_fee' => 75.00, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'booking_id' => $booking->id,
                    'service_id' => 2, 
                    'service_fee' => 75.00, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
