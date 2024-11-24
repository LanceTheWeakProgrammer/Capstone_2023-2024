<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Technician;
use Exception;

class TechnicianService
{
    public function manageQuota(int $technicianId)
    {
        try {
            $inactiveStatuses = ['Cancelled', 'Declined', 'Completed', 'No Show'];

            $activeBookingsCount = Booking::where('technician_id', $technicianId)
                ->whereNotIn('status', $inactiveStatuses)
                ->count();

            if ($activeBookingsCount >= 5) {
                Technician::where('id', $technicianId)->update(['avail_status' => false]);
                $technician = Technician::find($technicianId);
                if ($technician) {
                    $technician->user()->update(['status' => 'busy']);
                }
            } else {
                Technician::where('id', $technicianId)->update(['avail_status' => true]);
                $technician = Technician::find($technicianId);
                if ($technician) {
                    $technician->user()->update(['status' => 'active']);
                }
            }
        } catch (Exception $e) {
            throw new Exception('Error managing technician quota: ' . $e->getMessage());
        }
    }
}
