<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\v1\AdminDashboardController;
use App\Http\Controllers\v1\ApprovalController;
use App\Http\Controllers\v1\BookingStatusController;
use App\Http\Controllers\v1\CarouselController;
use App\Http\Controllers\v1\ManageBookingAsAdminController;
use App\Http\Controllers\v1\ManageBookingAsTechnicianController;
use App\Http\Controllers\v1\ManageProfileAsTechnicianController;
use App\Http\Controllers\v1\ManageProfileAsUserController;
use App\Http\Controllers\v1\ManageTechnicianAsAdminController;
use App\Http\Controllers\v1\ManageUserController;
use App\Http\Controllers\v1\MessageController;
use App\Http\Controllers\v1\NotificationController;
use App\Http\Controllers\v1\PaymentController;
use App\Http\Controllers\v1\PublicDataController;
use App\Http\Controllers\v1\RatingController;
use App\Http\Controllers\v1\ServiceController;
use App\Http\Controllers\v1\SettingsController;
use App\Http\Controllers\v1\TestimonialController;
use App\Http\Controllers\v1\UserBookingController;
use App\Http\Controllers\v1\VehicleController;

// USER routes (some routes can be used in all roles)
Route::prefix('v1')->group(function () {
    // Public data routes
    Route::get('/app-info', [PublicDataController::class, 'getAppInfo']);
    Route::get('/carousel', [PublicDataController::class, 'getCarousel']);
    Route::get('/contact-info', [PublicDataController::class, 'getContactInfo']);
    Route::get('/services', [PublicDataController::class, 'getServices']);
    Route::get('/technicians', [PublicDataController::class, 'getTechnicians']);
    Route::get('/team-info', [PublicDataController::class, 'getTeamInfo']);
    Route::get('/technicians/{id}', [PublicDataController::class, 'showTechnician']);
    Route::get('/testimonials', [PublicDataController::class, 'getTestimonials']);

    Route::get('/vehicle', [VehicleController::class, 'index']);  
    Route::get('/vehicle/types', [VehicleController::class, 'getVehicleTypes']);
    Route::get('/vehicle/details/{type_id}', [VehicleController::class, 'getVehicleDetails']);
    Route::get('vehicle/type/{id}', [VehicleController::class, 'getVehicleTypeById']);

    //Testimonial
    Route::post('/testimonials', [TestimonialController::class, 'store']);

    // Registration
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login and logout
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // Change password
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);

    // Email Verification (by code)
    Route::post('/verify-email', [VerifyEmailController::class, 'verify']);

    // Protected routes (e.g., bookings, messages, etc.)
    Route::middleware('auth:sanctum')->group(function () {
        // Bookings
        Route::post('/booking/registered', [UserBookingController::class, 'store']);
        Route::get('/booking', [UserBookingController::class, 'index']);
        Route::get('/booking/{id}', [UserBookingController::class, 'show']);
        Route::put('/bookings/{bookingId}/cancel-request', [UserBookingController::class, 'requestCancel']);
        Route::post('/bookings/{bookingId}/request-reschedule', [UserBookingController::class, 'requestReschedule']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{notificationId}', [NotificationController::class, 'destroy']);

        // Messages
        Route::post('/messages/send', [MessageController::class, 'sendMessage']);
        Route::get('/messages/{userId}', [MessageController::class, 'receiveMessages']);
        Route::put('/messages/mark-as-read/{messageId?}', [MessageController::class, 'markAsRead']);

        // Profile
        Route::get('/users/{id}', [ManageProfileAsUserController::class, 'show']);
        Route::put('/users/{id}', [ManageProfileAsUserController::class, 'update']);

        // Payment
        Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent']);
        Route::post('/payment/attach-method', [PaymentController::class, 'attachPaymentIntent']);
        Route::get('payment/retrieve-intent', [PaymentController::class, 'retrievePaymentIntent']);

        // Ratings
        Route::get('/ratings', [RatingController::class, 'index']);
        Route::get('/ratings/{id}', [RatingController::class, 'show']);
        Route::post('/ratings', [RatingController::class, 'store']);
        Route::delete('/ratings/{id}', [RatingController::class, 'delete']);
    });
});

// ADMIN routes
Route::prefix('admin/v1')->group(function () {

    // Login and logout
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // Protected routes for authenticated admin users
    Route::middleware('auth:sanctum')->group(function () {

        // Dashboard data
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboardData']);
        Route::get('/dashboard/filter', [AdminDashboardController::class, 'filterBookings']);

        // Booking management 
        Route::get('/bookings/registered', [ManageBookingAsAdminController::class, 'getRegisteredBookings']);
        Route::get('/bookings/active', [ManageBookingAsAdminController::class, 'getActiveBookings']);

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

        //Testimonial management
        Route::get('/testimonials', [TestimonialController::class, 'index']); 
        Route::put('/testimonials/{id}/approve', [TestimonialController::class, 'approve']);
    });
});

// TECHNICIAN routes
Route::prefix('technician/v1')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::middleware('auth:sanctum')->group(function () {
        // Manage bookings
        Route::get('/bookings', [ManageBookingAsTechnicianController::class, 'index']);
        Route::put('/bookings/{bookingId}/approve', [ApprovalController::class, 'approveBooking']);
        Route::put('/bookings/{bookingId}/decline', [ApprovalController::class, 'declineBooking']);
        Route::post('/bookings/{bookingId}/reschedule', [ManageBookingAsTechnicianController::class, 'reschedule']);
        Route::put('/bookings/{bookingId}/in-progress', [BookingStatusController::class, 'inProgress']);
        Route::put('/bookings/{bookingId}/completed', [BookingStatusController::class, 'completed']);
        Route::put('/bookings/{bookingId}/no-show', [BookingStatusController::class, 'noShow']);

        Route::put('/bookings/{bookingId}/approve-reschedule', [ApprovalController::class, 'approveRescheduleRequest']);
        Route::post('/bookings/{bookingId}/decline-reschedule', [ApprovalController::class, 'declineRescheduleRequest']);
        Route::put('/bookings/{bookingId}/approve-cancel', [ApprovalController::class, 'approveCancelRequest']);
        Route::post('/bookings/{bookingId}/decline-cancel', [ApprovalController::class, 'declineCancelRequest']);

        Route::put('/technician/{technicianId}/toggle-availability/{status}', [ManageBookingAsTechnicianController::class, 'toggleAvailability']);

        // Change password
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);

        // Technician profile management
        Route::get('/technicians/{id}', [ManageProfileAsTechnicianController::class, 'show']);
        Route::put('/technicians/{id}', [ManageProfileAsTechnicianController::class, 'update']);
    });
});
