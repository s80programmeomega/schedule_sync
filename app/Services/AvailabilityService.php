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
        $availability = Availability::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();

        if ($availability->isEmpty()) {
            return [];
        }

        // Get existing bookings for this date
        $existingBookings = Booking::where('user_id', $userId)
            ->whereDate('start_time', $date)
            ->where('status', 'scheduled')
            ->get(['start_time', 'end_time']);

        return $this->generateTimeSlots($availability, $existingBookings, $duration, $bufferBefore, $bufferAfter, $date);
    }

    /**
     * Check if a specific time slot is available
     *
     * @param int $userId
     * @param Carbon $startTime
     * @param int $duration
     * @return bool
     */
    public function isSlotAvailable(int $userId, Carbon $startTime, int $duration): bool
    {
        $endTime = $startTime->copy()->addMinutes($duration);
        $dayOfWeek = strtolower($startTime->format('l'));

        // Check if user has availability for this day and time
        $hasAvailability = Availability::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->where('start_time', '<=', $startTime->format('H:i'))
            ->where('end_time', '>=', $endTime->format('H:i'))
            ->exists();

        if (!$hasAvailability) {
            return false;
        }

        // Check for booking conflicts
        $hasConflict = Booking::where('user_id', $userId)
            ->where('status', 'scheduled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })->exists();

        return !$hasConflict;
    }

    /**
     * Generate time slots from availability periods
     *
     * @param \Illuminate\Database\Eloquent\Collection $availability
     * @param \Illuminate\Database\Eloquent\Collection $existingBookings
     * @param int $duration
     * @param int $bufferBefore
     * @param int $bufferAfter
     * @param Carbon $date
     * @return array
     */
    private function generateTimeSlots($availability, $existingBookings, $duration, $bufferBefore, $bufferAfter, $date): array
    {
        $slots = [];
        $now = now();

        foreach ($availability as $period) {
            $start = $date->copy()->setTimeFromTimeString($period->start_time);
            $end = $date->copy()->setTimeFromTimeString($period->end_time);

            // Skip if the entire period is in the past
            if ($end->isPast()) {
                continue;
            }

            // Adjust start time if it's in the past
            if ($start->isPast()) {
                $start = $now->copy()->addMinutes(15)->startOfMinute();
            }

            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $slotEnd = $start->copy()->addMinutes($duration);

                // Check if slot conflicts with existing bookings
                $hasConflict = false;
                foreach ($existingBookings as $booking) {
                    $bookingStart = Carbon::parse($booking->start_time)->subMinutes($bufferBefore);
                    $bookingEnd = Carbon::parse($booking->end_time)->addMinutes($bufferAfter);

                    if ($start->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                        $hasConflict = true;
                        break;
                    }
                }

                if (!$hasConflict) {
                    $slots[] = [
                        'start_time' => $start->toISOString(),
                        'end_time' => $slotEnd->toISOString(),
                        'formatted_time' => $start->format('g:i A') . ' - ' . $slotEnd->format('g:i A')
                    ];
                }

                $start->addMinutes(15); // 15-minute intervals
            }
        }

        return $slots;
    }
}
