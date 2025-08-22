<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\v1\DashboardController;
use App\Http\Controllers\web\v1\Auth\LoginController;
use App\Http\Controllers\web\v1\Auth\RegisterController;
use App\Http\Controllers\web\v1\EventTypeController;
use App\Http\Controllers\web\v1\BookingController;
use App\Http\Controllers\web\v1\AvailabilityController;
use App\Http\Controllers\web\v1\ProfileController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/home', function () {
    return view('home');
});




// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


Route::middleware('auth')->group(function () {
    Route::resource('dashboard', DashboardController::class)->only(['index']);
});

Route::middleware('auth')->group(function () {
    Route::resource('event-types', EventTypeController::class);
});

Route::middleware('auth')->group(function () {
    Route::resource('bookings', BookingController::class);
    Route::get('bookings-scheduled', [BookingController::class, 'scheduled'])->name('bookings.scheduled');
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

Route::middleware('auth')->group(function () {
    Route::resource('availability', AvailabilityController::class);
});

// Public Booking Routes
Route::prefix('book')->name('public.booking.')->group(function () {
    Route::get('{username}', function ($username) {
        return view('public.booking.create', ['username' => $username]);
    })->name('create');

    Route::get('{booking}/cancel', function ($booking) {
        return view('public.booking.cancel', ['booking' => $booking]);
    })->name('cancel');

    Route::get('{booking}/reschedule', function ($booking) {
        return view('public.booking.reschedule', ['booking' => $booking]);
    })->name('reschedule');
});
