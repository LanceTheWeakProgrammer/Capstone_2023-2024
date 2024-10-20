<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\v1\SettingsController;
use App\Http\Controllers\v1\CarouselController;
use App\Http\Controllers\v1\ServiceController;
use App\Http\Controllers\v1\VehicleController;
use App\Http\Controllers\v1\ManageTechnicianAsAdminController;
use App\Http\Controllers\v1\UserBookingController;
use App\Http\Controllers\v1\ManageUserController; 
use App\Http\Controllers\v1\ManageBookingAsAdminController;
use App\Http\Controllers\v1\ManageBookingAsTechnicianController;
use App\Http\Controllers\Auth\ChangePasswordController;

// ADMIN routes
Route::prefix('admin/v1')->group(function () {

    // Login and logout
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // Protected routes for authenticated admin users
    Route::middleware('auth:sanctum')->group(function () {

        // Booking management
        Route::get('/bookings/registered', [ManageBookingAsAdminController::class, 'getRegisteredBookings']);
        Route::get('/bookings/guest', [ManageBookingAsAdminController::class, 'getGuestBookings']);
        Route::post('/bookings/{bookingId}/assign-technician', [ManageBookingAsAdminController::class, 'assignTechnicianToGuest']);

        // Application settings
        Route::get('/settings/app', [SettingsController::class, 'indexApp']);
        Route::put('/settings/app', [SettingsController::class, 'updateApp']);

        // Contact settings
        Route::get('/settings/contact', [SettingsController::class, 'indexContacts']);
        Route::put('/settings/contact', [SettingsController::class, 'updateContact']);

        // Team management
        Route::get('/teams', [SettingsController::class, 'indexTeams']);
        Route::post('/teams', [SettingsController::class, 'storeTeam']);
        Route::delete('/teams/{id}', [SettingsController::class, 'destroyTeam']);

        // Carousel management
        Route::get('/carousel', [CarouselController::class, 'index']);
        Route::post('/carousel', [CarouselController::class, 'store']);
        Route::put('/carousel/toggle/{id}', [CarouselController::class, 'toggle']);

        // Services management
        Route::get('/service', [ServiceController::class, 'index']);
        Route::post('/service', [ServiceController::class, 'store']);
        Route::delete('/service/{id}', [ServiceController::class, 'destroy']);

        // Vehicle management
        Route::get('/vehicle', [VehicleController::class, 'index']);  
        Route::get('/vehicle/types', [VehicleController::class, 'getVehicleTypes']);
        Route::post('/vehicle', [VehicleController::class, 'store']);
        Route::delete('/vehicle/{id}', [VehicleController::class, 'destroy']);
        Route::get('/vehicle/details/{type_id}', [VehicleController::class, 'getVehicleDetails']);

        // Technician management
        Route::get('/technician', [ManageTechnicianAsAdminController::class, 'index']);
        Route::get('/technician/{id}', [ManageTechnicianAsAdminController::class, 'show']);
        Route::post('/technician', [ManageTechnicianAsAdminController::class, 'store']);
        Route::post('/technician/update', [ManageTechnicianAsAdminController::class, 'update']);
        Route::put('/technician/toggle-active/{id}', [ManageTechnicianAsAdminController::class, 'toggle']);
        Route::put('/technician/{id}', [ManageTechnicianAsAdminController::class, 'remove']);

        // User management
        Route::get('/users', [ManageUserController::class, 'index']); 
    });
});

// USER routes
Route::prefix('v1')->group(function () {
    // Registration
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login and logout
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Change password
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);

    Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // Email Verification (by code)
    Route::post('/verify-email', [VerifyEmailController::class, 'verify']);

    // Public access routes
    Route::get('/carousel', [CarouselController::class, 'index']);
    Route::get('/service', [ServiceController::class, 'index']);
    
    Route::get('/vehicle', [VehicleController::class, 'index']);  
    Route::get('/vehicle/types', [VehicleController::class, 'getVehicleTypes']);
    Route::get('/vehicle/details/{type_id}', [VehicleController::class, 'getVehicleDetails']);
    Route::get('vehicle/type/{id}', [VehicleController::class, 'getVehicleTypeById']);

    Route::get('/technician', [ManageTechnicianAsAdminController::class, 'index']);
    Route::get('/technician/{id}', [ManageTechnicianAsAdminController::class, 'show']);

    Route::get('/settings/contact', [SettingsController::class, 'indexContacts']);
    Route::get('/teams', [SettingsController::class, 'indexTeams']);

    //Booking
    Route::post('/booking/guest', [UserBookingController::class, 'storeForGuestUser']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/booking/registered', [UserBookingController::class, 'storeForRegisteredUser']);
        Route::post('/booking/transfer', [UserBookingController::class, 'transfer']);
        Route::get('/booking', [UserBookingController::class, 'show']);
        Route::put('bookings/{bookingId}/cancel-request', [UserBookingController::class, 'requestCancel']);
    });
});


// TECHNICIAN routes
Route::prefix('technician/v1')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/bookings', [ManageBookingAsTechnicianController::class, 'index']);
        Route::put('/bookings/{bookingId}/approve', [ManageBookingAsTechnicianController::class, 'approveBooking']);
        Route::put('/bookings/{bookingId}/decline', [ManageBookingAsTechnicianController::class, 'declineBooking']);

        // Change password
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);
    });
});
