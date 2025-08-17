<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\v1\DashboardController;
use App\Http\Controllers\web\v1\Auth\LoginController;
use App\Http\Controllers\web\v1\Auth\RegisterController;
use App\Http\Controllers\web\v1\EventTypeController;
use App\Http\Controllers\web\v1\BookingController;
use App\Http\Controllers\web\v1\AvailabilityController;

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