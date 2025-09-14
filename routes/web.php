<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\v1\DashboardController;
use App\Http\Controllers\web\v1\Auth\LoginController;
use App\Http\Controllers\web\v1\Auth\RegisterController;
use App\Http\Controllers\web\v1\EventTypeController;
use App\Http\Controllers\web\v1\BookingController;
use App\Http\Controllers\web\v1\AvailabilityController;
use App\Http\Controllers\web\v1\ProfileController;
use App\Http\Controllers\web\v1\Auth\EmailVerificationController;
use App\Http\Controllers\web\v1\Auth\PasswordResetController;
use App\Http\Controllers\web\v1\TeamController;
use App\Http\Controllers\web\v1\TeamMemberController;
use App\Http\Controllers\web\v1\ContactController;
use App\Http\Controllers\web\v1\GroupController;
use App\Http\Controllers\web\v1\GroupMemberController;

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

// Email Verification Routes
Route::middleware('auth')->group(function () {
    // Show email verification notice
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    // Handle email verification
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resend verification email
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Profile Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dashboard', DashboardController::class)->only(['index']);
})->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('event-types', EventTypeController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('bookings/scheduled', [BookingController::class, 'scheduled'])->name('bookings.scheduled');
    Route::get('bookings/completed', [BookingController::class, 'completed'])->name('bookings.completed');
    Route::get('bookings/cancelled', [BookingController::class, 'cancelled'])->name('bookings.cancelled');

    // Attendee management routes
    Route::post('bookings/{booking}/attendees', [BookingController::class, 'addAttendee'])->name('bookings.attendees.add');
    Route::delete('bookings/{booking}/attendees/{attendee}', [BookingController::class, 'removeAttendee'])->name('bookings.attendees.remove');
    Route::patch('bookings/{booking}/attendees/{attendee}', [BookingController::class, 'updateAttendee'])->name('bookings.attendees.update');
    Route::get('bookings/{booking}/attendees', [BookingController::class, 'getAttendees'])->name('bookings.attendees.get');


    // Resource Routes come always at the end
    Route::resource('bookings', BookingController::class);
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('availability', AvailabilityController::class);
    Route::get('availability/{availability}/slots', [AvailabilityController::class, 'slots'])->name('availability.slots');
});


// Team management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('teams', TeamController::class);
    Route::resource('teams.members', TeamMemberController::class)->except(['show']);
    Route::get('/teams/{team}/members', [TeamMemberController::class, 'index'])->name('teams.members.index');
    Route::resource('groups', GroupController::class);

    Route::resource('groups.members', GroupMemberController::class)->except(['show']);
    Route::get('/groups/{group}/members', [GroupMemberController::class, 'index'])->name('groups.members.index');

    // AJAX endpoints for member fetching
    Route::get('/teams/{team}/members-json', [TeamController::class, 'getMembers'])->name('teams.members.json');
    Route::get('/groups/{group}/members-json', [GroupController::class, 'getMembers'])->name('groups.members.json');

    // Enhanced contact endpoints
    Route::get('/contacts/all-json', [ContactController::class, 'getAllContacts'])->name('contacts.all.json');

    Route::resource('contacts', ContactController::class);
});

// Public team invitation acceptance
Route::get('/team-invitation/{token}', [TeamMemberController::class, 'acceptInvitation'])->name('team.invitation.accept');



Route::middleware(['auth', 'verified'])->group(function () {
    // Enhanced booking routes
    Route::get('bookings/create-with-attendees', [BookingController::class, 'createWithAttendees'])->name('bookings.create-with-attendees');
    Route::post('bookings/with-attendees', [BookingController::class, 'storeWithAttendees'])->name('bookings.store-with-attendees');

    // Enhanced booking with multiple attendees
    Route::post('bookings/with-attendees', [BookingController::class, 'storeWithAttendees'])->name('bookings.store-with-attendees');

});


// // Public Booking Routes
// Route::prefix('book')->name('public.booking.')->group(function () {
//     Route::get('{username}', function ($username) {
//         return view('public.booking.create', ['username' => $username]);
//     })->name('create');

//     Route::get('{booking}/cancel', function ($booking) {
//         return view('public.booking.cancel', ['booking' => $booking]);
//     })->name('cancel');

//     Route::get('{booking}/reschedule', function ($booking) {
//         return view('public.booking.reschedule', ['booking' => $booking]);
//     })->name('reschedule');
// });
