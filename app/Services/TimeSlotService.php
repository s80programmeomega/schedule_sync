<?php

namespace App\Services;

use App\Models\Availability;
use Carbon\Carbon;

class TimeSlotService
{
    public function generateTimeSlots(Availability $availability, int $duration = 30): array
    {
        // 1. Get existing bookings
        $bookings = $availability->bookings()->get();
        $bookedPeriods = [];

        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->start_time);
            $bookingEnd = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->end_time);

            $bookedPeriods[] = [
                'start' => $bookingStart,
                'end' => $bookingEnd,
                'booking' => $booking
            ];
        }

        // 2. Generate available slots with proper gaps
        $slots = [];
        $start = Carbon::parse($availability->availability_date->toDateString() . ' ' . $availability->getRawOriginal('start_time'));
        $end = Carbon::parse($availability->availability_date->toDateString() . ' ' . $availability->getRawOriginal('end_time'));

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $start->copy()->addMinutes($duration);

            // Check if slot conflicts with bookings (including 30min buffer)
            $isBlocked = collect($bookedPeriods)->contains(function ($booked) use ($start, $slotEnd) {
                return $start->lt($booked['end']->copy()->addMinutes(30)) &&
                    $slotEnd->gt($booked['start']->copy()->subMinutes(30));
            });

            if (!$isBlocked) {
                $slots[] = [
                    'start_time' => $start->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'formatted_time' => $start->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'is_occupied' => false,
                    'booking' => null
                ];

                $start->addMinutes($duration + 30);
            } else {
                $blockingPeriod = collect($bookedPeriods)->first(function ($booked) use ($start, $slotEnd) {
                    return $start->lt($booked['end']->copy()->addMinutes(30)) &&
                        $slotEnd->gt($booked['start']->copy()->subMinutes(30));
                });
                $start = $blockingPeriod['end']->copy()->addMinutes(30);
            }
        }

        // 3. Add booking slots
        foreach ($bookedPeriods as $booked) {
            $slots[] = [
                'start_time' => $booked['start']->format('H:i'),
                'end_time' => $booked['end']->format('H:i'),
                'formatted_time' => $booked['start']->format('g:i A') . ' - ' . $booked['end']->format('g:i A'),
                'is_occupied' => true,
                'booking' => $booked['booking']
            ];
        }

        return collect($slots)->sortBy('start_time')->values()->all();
    }
}
