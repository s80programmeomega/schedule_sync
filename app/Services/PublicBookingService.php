<?php
// app/Services/PublicBookingService.php

namespace App\Services;

use App\Models\User;
use App\Models\EventType;
use App\Models\Booking;
use App\Models\Availability;
use App\Mail\BookingApproved;
use App\Mail\BookingRejected;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

/**
 * Public Booking Service
 * Handles business logic for public booking operations
 */
class PublicBookingService
{
    /**
     * Check if time slot is available
     */
    public function isTimeSlotAvailable(User $user, Carbon $startTime, Carbon $endTime, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('user_id', $user->id)
            ->whereIn('status', ['scheduled', 'pending'])
            ->whereIn('approval_status', ['approved', 'pending'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    /**
     * Create booking request
     */
    public function createBookingRequest(EventType $eventType, array $data): Booking
    {
        $startTime = Carbon::parse($data['start_time']);
        $endTime = $startTime->copy()->addMinutes($eventType->duration);

        if (!$this->isTimeSlotAvailable($eventType->user, $startTime, $endTime)) {
            throw new \Exception('Time slot is no longer available');
        }

        return Booking::create([
            'event_type_id' => $eventType->id,
            'user_id' => $eventType->user_id,
            'attendee_name' => $data['attendee_name'],
            'attendee_email' => $data['attendee_email'],
            'attendee_phone' => $data['attendee_phone'] ?? null,
            'attendee_notes' => $data['attendee_notes'] ?? null,
            'booking_date' => $data['booking_date'],
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'status' => $eventType->requires_approval ? 'pending' : 'scheduled',
            'approval_status' => $eventType->requires_approval ? 'pending' : 'approved',
            'approved_at' => $eventType->requires_approval ? null : now(),
        ]);
    }

    /**
     * Approve booking and send notification
     */
    public function approveBooking(Booking $booking): bool
    {
        if (!$booking->isPendingApproval()) {
            return false;
        }

        $booking->approve();
        Mail::to($booking->attendee_email)->send(new BookingApproved($booking));

        return true;
    }

    /**
     * Reject booking and send notification
     */
    public function rejectBooking(Booking $booking, ?string $reason = null): bool
    {
        if (!$booking->isPendingApproval()) {
            return false;
        }

        $booking->reject($reason);
        Mail::to($booking->attendee_email)->send(new BookingRejected($booking));

        return true;
    }

    /**
     * Get available time slots for a date
     */
    public function getAvailableSlots(User $user, EventType $eventType, Carbon $date): array
    {
        $availability = Availability::where('user_id', $user->id)
            ->whereDate('availability_date', $date)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();

        if ($availability->isEmpty()) {
            return [];
        }

        $existingBookings = Booking::where('user_id', $user->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['scheduled', 'pending'])
            ->whereIn('approval_status', ['approved', 'pending'])
            ->get(['start_time', 'end_time']);

        return $this->generateTimeSlots($availability, $existingBookings, $eventType, $date);
    }

    /**
     * Generate available time slots
     */
    private function generateTimeSlots($availability, $existingBookings, EventType $eventType, Carbon $date): array
    {
        $slots = [];
        $now = now();

        foreach ($availability as $period) {
            $start = $date->copy()->setTimeFromTimeString($period->start_time);
            $end = $date->copy()->setTimeFromTimeString($period->end_time);

            if ($end->isPast()) continue;
            if ($start->isPast()) {
                $start = $now->copy()->addMinutes(15)->startOfMinute();
            }

            while ($start->copy()->addMinutes($eventType->duration)->lte($end)) {
                $slotEnd = $start->copy()->addMinutes($eventType->duration);

                $hasConflict = $existingBookings->contains(function ($booking) use ($start, $slotEnd) {
                    $bookingStart = Carbon::parse($booking->start_time);
                    $bookingEnd = Carbon::parse($booking->end_time);
                    return $start->lt($bookingEnd) && $slotEnd->gt($bookingStart);
                });

                if (!$hasConflict) {
                    $slots[] = [
                        'start_time' => $start->toISOString(),
                        'formatted_time' => $start->format('g:i A'),
                    ];
                }

                $start->addMinutes(15);
            }
        }

        return $slots;
    }
}
