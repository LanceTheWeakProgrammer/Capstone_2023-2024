<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\UserProfile;
use App\Models\Technician;
use App\Models\Service;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function dashboardData(Request $request)
    {
        try {
            $filter = $request->query('filter', 'all'); 
            $selectedYear = $request->query('year', null);
    
            $dateRange = $filter !== 'all' ? $this->getDateRange($filter) : null;
    
            // Filtered Bookings
            $filteredBookingsQuery = Booking::query();
            if ($dateRange) {
                $filteredBookingsQuery->whereBetween('booking_date', [$dateRange['start'], $dateRange['end']]);
            }
    
            if ($selectedYear) {
                $filteredBookingsQuery->whereYear('booking_date', $selectedYear);
            }
    
            // Filtered Technicians
            $filteredTechniciansQuery = Technician::query();
            if ($dateRange) {
                $filteredTechniciansQuery->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
    
            if ($selectedYear) {
                $filteredTechniciansQuery->whereYear('created_at', $selectedYear);
            }
    
            // Filtered Users
            $filteredUsersQuery = UserProfile::query();
            if ($dateRange) {
                $filteredUsersQuery->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
    
            if ($selectedYear) {
                $filteredUsersQuery->whereYear('created_at', $selectedYear);
            }
    
            // Totals
            $totalSales = Payment::where('status', 'Paid')
                ->when($dateRange, function ($query) use ($dateRange) {
                    $query->whereBetween('payment_date', [$dateRange['start'], $dateRange['end']]);
                })
                ->when($selectedYear, function ($query) use ($selectedYear) {
                    $query->whereYear('payment_date', $selectedYear);
                })
                ->sum('amount');
    
            $totalTechnicians = $filteredTechniciansQuery->count();
            $totalBookings = $filteredBookingsQuery->count();
            $totalUsers = $filteredUsersQuery->count();
    
            $months = collect([
                'January' => 0, 'February' => 0, 'March' => 0, 'April' => 0,
                'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0,
                'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0,
            ]);
    
            // Monthly Bookings 
            $rawMonthlyBookings = Booking::query()
                ->selectRaw('YEAR(booking_date) as year, MONTHNAME(booking_date) as month_name, MONTH(booking_date) as month_number, COUNT(*) as count')
                ->groupByRaw('YEAR(booking_date), MONTHNAME(booking_date), MONTH(booking_date)')
                ->orderByRaw('year ASC, month_number ASC')
                ->get();
    
            $monthlyBookings = $rawMonthlyBookings->groupBy('year')->map(function ($yearData) use ($months) {
                $yearlyData = $months->map(function () {
                    return 0;
                });
    
                $yearData->each(function ($data) use (&$yearlyData) {
                    $yearlyData[$data['month_name']] = $data['count'];
                });
    
                return $yearlyData;
            });
    
            // Top 5 Vehicle Types
            $topVehicleTypes = $filteredBookingsQuery
                ->join('vehicle_details', 'bookings.vehicle_detail_id', '=', 'vehicle_details.id')
                ->join('vehicle_types', 'vehicle_details.vehicle_type_id', '=', 'vehicle_types.id')
                ->selectRaw('vehicle_types.type, COUNT(bookings.id) as count')
                ->groupBy('vehicle_types.type')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();
    
            // Top 5 Services
            $topServices = $filteredBookingsQuery
                ->join('services_selected', 'bookings.id', '=', 'services_selected.booking_id')
                ->join('service', 'service.id', '=', 'services_selected.service_id')
                ->selectRaw('service.name, service.icon, COUNT(services_selected.id) as count')
                ->groupBy('service.id', 'service.name', 'service.icon')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();
    
            return response()->json([
                'total_sales' => $totalSales,
                'total_technicians' => $totalTechnicians,
                'total_bookings' => $totalBookings,
                'total_users' => $totalUsers,
                'monthly_bookings' => $monthlyBookings,
                'top_vehicles' => $topVehicleTypes,
                'top_services' => $topServices,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    
            return response()->json([
                'error' => 'Unable to fetch dashboard data. Please try again later.'
            ], 500);
        }
    }
    

    private function getDateRange($filter)
    {
        $now = now();

        switch ($filter) {
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                ];
            case '3_months':
                return [
                    'start' => $now->copy()->subMonths(3)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                ];
            case '6_months':
                return [
                    'start' => $now->copy()->subMonths(6)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                ];
            case 'all':
            default:
                return null;
        }
    }
}
