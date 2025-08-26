<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\EventType;
use Carbon\Carbon;

/**
 * Availability Service
 *
 * Handles complex availability calculations and time slot generation.
 * Centralizes availability logic for reuse across controllers.
 */
class AvailabilityService
{
    /**
     * Get available time slots for a user on a specific date
     *
     * @param int $userId
     * @param string $date
     * @param int $duration
     * @param int $bufferBefore
     * @param int $bufferAfter
     * @return array
     */
    public function getAvailableSlots(int $userId, string $date, int $duration, int $bufferBefore = 0, int $bufferAfter = 0): array
    {
        $date = Carbon::parse($date);
        $dayOfWeek = strtolower($date->format('l'));

        // Get user's availability for this day
        $availabilities = Availability::where('user_id', $userId)
            ->where('availability_date', $date)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();

        if ($availabilities->isEmpty()) {
            return [];
        }

        // Get existing bookings for this date
        $existingBookings = Booking::where('user_id', $userId)
            ->whereDate('booking_date', $date)
            ->where('status', 'scheduled')
            ->get(['start_time', 'end_time']);

        return $this->generateTimeSlots($availabilities, $existingBookings, $duration, $bufferBefore, $bufferAfter, $date);
    }

    /**
     * Check if a specific time slot is available
     *
     * @param int $userId
     * @param Carbon $startTime
     * @param int $duration
     * @return bool
     */
    // public function isSlotAvailable(int $userId, string $date, string $startTime, int $duration): bool
    // {
    //     $start_time = Carbon::parse($startTime)->format('H:i');
    //     $end_time = Carbon::parse($start_time)->addMinutes($duration)->format('H:i');
    //     $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

    //     // Check if user has availability for this day and time
    //     $hasAvailability = Availability::where('user_id', $userId)
    //         ->where('day_of_week', $dayOfWeek)
    //         ->where('is_available', true)
    //         ->where('start_time', '<=', $start_time)
    //         ->where('end_time', '>=', $end_time)
    //         ->where('is_available', true)
    //         ->exists();


    //     if (!$hasAvailability) {
    //         return false;
    //     }

    //     // Check for booking conflicts
    //     // $hasConflict = Booking::where('user_id', $userId)
    //     //     ->where('status', 'scheduled')
    //     //     ->where(function ($query) use ($startTime, $endTime) {
    //     //         $query->whereBetween('start_time', [$startTime, $endTime])
    //     //             ->orWhereBetween('end_time', [$startTime, $endTime])
    //     //             ->orWhere(function ($q) use ($startTime, $endTime) {
    //     //                 $q->where('start_time', '<=', $startTime)
    //     //                     ->where('end_time', '>=', $endTime);
    //     //             });
    //     //     })->exists();
    //     $conflicts = Booking::where('user_id', $userId)
    //         ->where('status', 'scheduled')
    //         ->where(function ($query) use ($start_time, $end_time) {
    //             $query->whereBetween('start_time', [$start_time, $end_time])
    //                 ->orWhereBetween('end_time', [$start_time, $end_time])
    //                 ->orWhere(function ($q) use ($start_time, $end_time) {
    //                     $q->where('start_time', '<=', $start_time)
    //                         ->where('end_time', '>=', $end_time);
    //                 });
    //         })->get();
    //     dd($conflicts->toArray());

    //     return !$conflicts->isEmpty();
    // }


    /**
     * Check if a specific time slot is available for booking
     *
     * @param int $userId - The user who owns the calendar
     * @param string $date - The date we want to book (e.g., '2024-01-15')
     * @param string $startTime - The start time we want to book (e.g., '14:30')
     * @param int $duration - How long the appointment is in minutes (e.g., 30)
     * @return bool - true if slot is available, false if not
     */
    public function isSlotAvailable(int $userId, string $date, string $startTime, int $duration): bool
    {
        // Step 1: Convert the input time to proper format
        $requestedStartTime = Carbon::parse($startTime)->format('H:i'); // e.g., '14:30'
        $requestedEndTime = Carbon::parse($startTime)->addMinutes($duration)->format('H:i'); // e.g., '15:00'
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l')); // e.g., 'monday'

        // Step 2: Check if user is available during this time on this day of week
        $userHasAvailability = Availability::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->where('start_time', '<=', $requestedStartTime)  // User starts work before or at requested time
            ->where('end_time', '>=', $requestedEndTime)      // User ends work after or at requested end time
            ->where('is_available', true)
            ->exists();

        // If user is not available during this time, return false immediately
        if (!$userHasAvailability) {
            return false; // User doesn't work during this time
        }

        // Step 3: Check if there are any existing bookings that conflict
        $conflictingBookings = Booking::where('user_id', $userId)
            ->whereDate('booking_date', $date)  // Only check bookings on the same date
            ->where('status', 'scheduled')      // Only check confirmed bookings
            ->where(function ($query) use ($requestedStartTime, $requestedEndTime) {
                // Check for 3 types of conflicts:

                // Conflict Type 1: Existing booking starts during our requested time
                $query->whereBetween('start_time', [$requestedStartTime, $requestedEndTime])

                    // Conflict Type 2: Existing booking ends during our requested time
                    ->orWhereBetween('end_time', [$requestedStartTime, $requestedEndTime])

                    // Conflict Type 3: Existing booking completely covers our requested time
                    ->orWhere(function ($subQuery) use ($requestedStartTime, $requestedEndTime) {
                        $subQuery->where('start_time', '<=', $requestedStartTime)
                            ->where('end_time', '>=', $requestedEndTime);
                    });
            })
            ->get();

        // Step 4: Return true if NO conflicts found, false if conflicts exist
        $hasNoConflicts = $conflictingBookings->isEmpty();

        return $hasNoConflicts;
    }



    /**
     * Generate time slots from availability periods
     *
     * @param \Illuminate\Database\Eloquent\Collection $availabilities
     * @param \Illuminate\Database\Eloquent\Collection $existingBookings
     * @param int $duration
     * @param int $bufferBefore
     * @param int $bufferAfter
     * @param Carbon $date
     * @return array
     */

    private function generateTimeSlots($availabilities, $existingBookings, $duration, $bufferBefore = 0, $bufferAfter = 0, $date): array
    {
        $slots = [];
        $currentTime = now();

        foreach ($availabilities as $availability) {
            // Use availability_date and time fields directly from model
            $periodStart = Carbon::parse($availability->availability_date->toDateString() . ' ' . $availability->start_time);
            $periodEnd = Carbon::parse($availability->availability_date->toDateString() . ' ' . $availability->end_time);

            // Skip if entire availability period is in the past
            if ($periodEnd->lte($currentTime)) {
                continue;
            }

            // Adjust start time if it's in the past
            if ($periodStart->lt($currentTime)) {
                $minutesToAdd = 15 - ($currentTime->minute % 15);
                $periodStart = $currentTime->copy()->addMinutes($minutesToAdd)->startOfMinute();
            }

            // Generate slots in 15-minute intervals
            $slotStart = $periodStart->copy();

            while ($slotStart->copy()->addMinutes($duration)->lte($periodEnd)) {
                $slotEnd = $slotStart->copy()->addMinutes($duration);

                // Check if slot conflicts with existing bookings
                $hasConflict = false;
                foreach ($existingBookings as $booking) {
                    // Skip bookings without booking_date
                    if (!$booking->booking_date) {
                        continue;
                    }

                    $bookingStart = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->start_time);
                    $bookingEnd = $bookingStart->copy()->addMinutes($booking->eventType->duration ?? $duration);

                    // Apply buffer times
                    $bufferedBookingStart = $bookingStart->copy()->subMinutes($bufferBefore);
                    $bufferedBookingEnd = $bookingEnd->copy()->addMinutes($bufferAfter);

                    if ($slotStart->lt($bufferedBookingEnd) && $slotEnd->gt($bufferedBookingStart)) {
                        $hasConflict = true;
                        break;
                    }
                }

                if (!$hasConflict) {
                    $slots[] = [
                        'start_time' => $slotStart->toISOString(),
                        'end_time' => $slotEnd->toISOString(),
                        'formatted_time' => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A')
                    ];
                }

                $slotStart->addMinutes(15);
            }
        }

        return $slots;
    }




    // private function generateTimeSlots($availabilities, $existingBookings, $duration, $bufferBefore, $bufferAfter, $date): array
    // {
    //     $slots = [];
    //     $now = now();

    //     foreach ($availabilities as $availability) {
    //         $start = $date->copy()->setTimeFromTimeString($availability->start_time);
    //         $end = $date->copy()->setTimeFromTimeString($availability->end_time);

    //         // Skip if the entire period is in the past
    //         if ($end->isPast()) {
    //             continue;
    //         }

    //         // Adjust start time if it's in the past
    //         if ($start->isPast()) {
    //             $start = $now->copy()->addMinutes(15)->startOfMinute();
    //         }

    //         while ($start->copy()->addMinutes($duration)->lte($end)) {
    //             $slotEnd = $start->copy()->addMinutes($duration);

    //             // Check if slot conflicts with existing bookings
    //             $hasConflict = false;
    //             foreach ($existingBookings as $booking) {
    //                 $bookingStart = Carbon::parse($booking->start_time)->subMinutes($bufferBefore);
    //                 $bookingEnd = Carbon::parse($booking->end_time)->addMinutes($bufferAfter);

    //                 if ($start->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
    //                     $hasConflict = true;
    //                     break;
    //                 }
    //             }

    //             if (!$hasConflict) {
    //                 $slots[] = [
    //                     'start_time' => $start->toISOString(),
    //                     'end_time' => $slotEnd->toISOString(),
    //                     'formatted_time' => $start->format('g:i A') . ' - ' . $slotEnd->format('g:i A')
    //                 ];
    //             }

    //             $start->addMinutes(15); // 15-minute intervals
    //         }
    //     }

    //     return $slots;
    // }
}
