<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendBookingReminders;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Console Commands and Scheduled Tasks for ScheduleSync
 */
Artisan::command('reminders:send {type}', function (string $type) {
    if (!in_array($type, ['24h', '1h'])) {
        $this->error('Type must be either "24h" or "1h"');
        return 1;
    }

    $this->info("Dispatching {$type} reminders...");
    dispatch(new SendBookingReminders($type));
    $this->info('Reminders dispatched successfully!');

    return 0;
})->purpose('Send booking reminders manually');

Artisan::command('bookings:cleanup', function () {
    $deleted = Booking::where('status', 'cancelled')
        ->where('cancelled_at', '<', now()->subDays(30))
        ->count();

    Booking::where('status', 'cancelled')
        ->where('cancelled_at', '<', now()->subDays(30))
        ->delete();

    $this->info("Cleaned up {$deleted} old cancelled bookings");

    return 0;
})->purpose('Clean up old cancelled bookings');

// Send 24-hour reminders every hour
Schedule::job(new SendBookingReminders('24h'))
    ->hourly()
    ->name('send-24h-reminders')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('24h reminders job completed successfully');
    })
    ->onFailure(function () {
        Log::error('24h reminders job failed');
    });

// Send 1-hour reminders every 15 minutes
Schedule::job(new SendBookingReminders('1h'))
    ->everyFifteenMinutes()
    ->name('send-1h-reminders')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('1h reminders job completed successfully');
    })
    ->onFailure(function () {
        Log::error('1h reminders job failed');
    });

// Clean up old cancelled bookings daily at 2 AM
Schedule::call(function () {
    $deleted = Booking::where('status', 'cancelled')
        ->where('cancelled_at', '<', now())
        ->count();

    Booking::where('status', 'cancelled')
        ->where('cancelled_at', '<', now())
        ->delete();

    Log::info("Cleaned up {$deleted} old cancelled bookings");
})->daily()->at('02:00')
    ->name('cleanup-old-bookings');

// Clean up expired availability slots daily at 2:30 AM
Schedule::call(function () {
    $deleted = \App\Models\Availability::where('availability_date', '<', now()->subDays(7))
        ->count();

    \App\Models\Availability::where('availability_date', '<', now()->subDays(7))
        ->delete();

    Log::info("Cleaned up {$deleted} old availability slots");
})->daily()->at('02:30')
    ->name('cleanup-old-availability');

// Generate daily statistics report
Schedule::call(function () {
    $stats = [
        'date' => today()->toDateString(),
        'total_bookings' => Booking::whereDate('created_at', today())->count(),
        'completed_bookings' => Booking::where('status', 'completed')
            ->whereDate('start_time', today())->count(),
        'cancelled_bookings' => Booking::where('status', 'cancelled')
            ->whereDate('cancelled_at', today())->count(),
        'active_event_types' => \App\Models\EventType::where('is_active', true)->count(),
    ];

    Log::info('Daily ScheduleSync statistics', $stats);
})->daily()->at('23:55')
    ->name('daily-stats');

// Weekly summary report (Sundays at 9 PM)
Schedule::call(function () {
    $weekStart = now()->startOfWeek();
    $weekEnd = now()->endOfWeek();

    $weeklyStats = [
        'week_of' => $weekStart->toDateString(),
        'total_bookings' => Booking::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
        'completed_meetings' => Booking::where('status', 'completed')
            ->whereBetween('start_time', [$weekStart, $weekEnd])->count(),
        'cancelled_meetings' => Booking::where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$weekStart, $weekEnd])->count(),
        'new_users' => \App\Models\User::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
    ];

    Log::info('Weekly ScheduleSync summary', $weeklyStats);
})->weekly()->sundays()->at('21:00')
    ->name('weekly-summary');
