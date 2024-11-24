<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppInfo;
use App\Models\Carousel;
use App\Models\ContactInfo;
use App\Models\Service;
use App\Models\Technician;
use App\Models\Testimonial;
use App\Models\TeamInfo;

class PublicDataController extends Controller
{
    public function getAppInfo()
    {
        $appInfo = AppInfo::all(); 
        return response()->json($appInfo);
    }

    public function getCarousel()
    {
        $carouselImages = Carousel::where('active', 1)->get(); 
        return response()->json($carouselImages);
    }

    public function getContactInfo()
    {
        $contactInfo = ContactInfo::all();
        return response()->json($contactInfo);
    }

    public function getServices()
    {
        $services = Service::all(); 
        return response()->json($services);
    }

    public function getTechnicians()
    {
        $technicians = Technician::with(['services', 'vehicleTypes', 'user', 'ratings'])
            ->where('is_removed', false) 
            ->where('avail_status', true) 
            ->get();

        return response()->json($technicians);
    }

    public function showTechnician($id)
    {
        try {
            $technician = Technician::with(['vehicleTypes', 'services', 'user', 'ratings.user'])->findOrFail($id);
    
            $technician->vehicle_type_ids = $technician->vehicleTypes->pluck('id');
            $technician->service_ids = $technician->services->pluck('id');
    
            return response()->json([
                'success' => true,
                'data' => $technician
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve technician: ' . $e->getMessage()
            ], 500);
        }
    }  

    public function getTeamInfo()
    {
        $teamInfo = TeamInfo::all(); 
        return response()->json($teamInfo);
    }

    public function getTestimonials()
    {
        try {
            $testimonials = Testimonial::with('user.profile')
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $testimonials,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch testimonials: ' . $e->getMessage(),
            ], 500);
        }
    }
}
