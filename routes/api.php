<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\EventTypeController;
use App\Http\Controllers\Api\v1\AvailabilityController;
use App\Http\Controllers\Api\v1\BookingController;
use App\Http\Controllers\Api\v1\PublicBookingController;
use App\Http\Controllers\Api\v1\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes - No authentication required
Route::prefix('v1')->group(function () {

    // Authentication endpoints
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public booking endpoints - for external users to book appointments
    Route::prefix('public')->group(function () {
        Route::get('users/{username}', [PublicBookingController::class, 'getUserProfile']);
        Route::get('users/{username}/event-types', [PublicBookingController::class, 'getEventTypes']);
        Route::get('users/{username}/event-types/{eventType}/availability', [PublicBookingController::class, 'getAvailability']);
        Route::post('users/{username}/event-types/{eventType}/book', [PublicBookingController::class, 'createBooking']);
        Route::get('bookings/{booking}/confirm', [PublicBookingController::class, 'confirmBooking']);
        // Route::get('bookings/{booking}/cancel', [PublicBookingController::class, 'showCancelBookingForm'])->name('public.booking.show_cancel');
        Route::post('bookings/{booking}/cancel', [PublicBookingController::class, 'cancelBooking'])->name('public.booking.cancel');
        Route::get('bookings/{booking}/reschedule', [PublicBookingController::class, 'rescheduleBooking'])->name('public.booking.reschedule');
    });
});

// Protected routes - Require authentication
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Authentication endpoints
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    // Dashboard and analytics
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/recent-bookings', [DashboardController::class, 'recentBookings']);

    // User profile management
    Route::prefix('user')->group(function () {
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile', [UserController::class, 'updateProfile']);
        Route::post('avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('avatar', [UserController::class, 'deleteAvatar']);
        Route::put('password', [UserController::class, 'updatePassword']);
        Route::delete('account', [UserController::class, 'deleteAccount']);
    });

    // Event Types management
    Route::apiResource('event-types', EventTypeController::class);
    Route::patch('event-types/{eventType}/toggle', [EventTypeController::class, 'toggle']);
    Route::post('event-types/{eventType}/duplicate', [EventTypeController::class, 'duplicate']);

    // Availability management
    Route::apiResource('availability', AvailabilityController::class);
    Route::post('availability/bulk', [AvailabilityController::class, 'bulkStore']);
    Route::put('availability/bulk', [AvailabilityController::class, 'bulkUpdate']);
    Route::delete('availability/bulk', [AvailabilityController::class, 'bulkDelete']);

    // Bookings management
    Route::apiResource('bookings', BookingController::class);
    Route::get('bookings-scheduled', [BookingController::class, 'scheduled']);
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::patch('bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    Route::patch('bookings/{booking}/complete', [BookingController::class, 'complete']);
    Route::patch('bookings/{booking}/no-show', [BookingController::class, 'markNoShow']);
    Route::get('bookings/{booking}/meeting-link', [BookingController::class, 'getMeetingLink']);
});
