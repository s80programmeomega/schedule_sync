<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Models\EventType;
use App\Models\Booking;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Http\Resources\v1\EventTypeResource;
use App\Http\Resources\v1\BookingResource;

/**
 * Public Booking Controller
 *
 * Handles public-facing booking operations for external users.
 * This is the core interface that visitors use to book appointments.
 *
 * Booking Flow:
 * 1. User visits /{username} - sees profile and event types
 * 2. User selects event type - sees available time slots
 * 3. User selects time slot - fills booking form
 * 4. System creates booking - sends confirmation
 *
 * Real-world example: Similar to Calendly's public booking flow
 */
class PublicBookingController extends ApiController
{
    /**
     * Get user profile and available event types
     *
     * @param string $username
     * @return JsonResponse
     */
    public function getUserProfile(string $username): JsonResponse
    {
        try {
            $user = User::where('username', $username)->first();

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $eventTypes = EventType::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('duration')
                ->get();

            return $this->successResponse([
                // 'user' => [
                //     'name' => $user->name,
                //     'username' => $user->username,
                //     'bio' => $user->bio,
                //     'timezone' => $user->timezone,
                //     'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                // ],
                'user' => new UserResource($user),
                'event_types' => EventTypeResource::collection($eventTypes)
            ], 'User profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user profile', 500);
        }
    }

    /**
     * Get available time slots for an event type
     *
     * @param string $username
     * @param EventType $eventType
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailability(string $username, EventType $eventType, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'timezone' => 'nullable|string'
            ]);

            $user = User::where('username', $username)->first();

            if (!$user || $eventType->user_id !== $user->id || !$eventType->is_active) {
                return $this->notFoundResponse('Event type not found or inactive or user not found');
            }

            $date = Carbon::parse($validated['date']);
            $dayOfWeek = strtolower($date->format('l'));

            // Get user's availability for this day
            $availability = Availability::where('user_id', $user->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->orderBy('start_time')
                ->get();

            // dd($availability);
            if ($availability->isEmpty()) {
                return $this->successResponse([
                    'available_slots' => [],
                    'message' => 'No availability for this date'
                ]);
            }

            // Get existing bookings for this date
            $existingBookings = Booking::where('user_id', $user->id)
                ->whereDate('booking_date', $date)
                // ->whereDate('start_time', $date)
                ->where('status', 'scheduled')
                ->get(['id', 'start_time', 'end_time', 'status']);


            // Generate available time slots
            $availableSlots = $this->generateTimeSlots(
                $availability,
                $existingBookings,
                $eventType->duration,
                $eventType->buffer_time_before ?? 0,
                $eventType->buffer_time_after ?? 0,
                $date
            );

            return $this->successResponse([
                'date' => $date->format('Y-m-d'),
                'available_slots' => $availableSlots,
                'event_type' => new EventTypeResource($eventType)
            ], 'Available slots retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve availability', 500);
        }
    }

    /**
     * Create a new booking
     *
     * @param Request $request
     * @param string $username
     * @param EventType $eventType
     * @return JsonResponse
     */
    public function createBooking(Request $request, string $username, EventType $eventType): JsonResponse
    {
        try {
            $validated = $request->validate([
                'attendee_name' => 'required|string|max:255',
                'attendee_email' => 'required|email|max:255',
                'attendee_notes' => 'nullable|string|max:1000',
                'start_time' => 'required|date|after:now',
                'timezone' => 'nullable|string'
            ]);

            $user = User::where('username', $username)->first();

            if (!$user || $eventType->user_id !== $user->id || !$eventType->is_active) {
                return $this->notFoundResponse('Event type not found or inactive');
            }

            $startTime = Carbon::parse($validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($eventType->duration);

            // Check for conflicts
            $conflict = Booking::where('user_id', $user->id)
                ->where('status', 'scheduled')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                })->exists();

            if ($conflict) {
                return $this->errorResponse('Time slot is no longer available', 422);
            }

            // Check daily booking limit
            if ($eventType->max_events_per_day) {
                $dailyBookings = Booking::where('user_id', $user->id)
                    ->where('event_type_id', $eventType->id)
                    ->whereDate('start_time', $startTime->format('Y-m-d'))
                    ->where('status', 'scheduled')
                    ->count();

                if ($dailyBookings >= $eventType->max_events_per_day) {
                    return $this->errorResponse('Daily booking limit reached for this event type', 422);
                }
            }

            // Create booking
            $booking = Booking::create([
                'event_type_id' => $eventType->id,
                'user_id' => $user->id,
                'attendee_name' => $validated['attendee_name'],
                'attendee_email' => $validated['attendee_email'],
                'attendee_notes' => $validated['attendee_notes'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $eventType->requires_confirmation ? 'pending' : 'scheduled',
                'meeting_link' => $this->generateMeetingLink($eventType)
            ]);

            // TODO: Send confirmation email
            // TODO: Add to calendar
            // TODO: Send notifications

            return $this->successResponse(
                new BookingResource($booking->load('eventType')),
                'Booking created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create booking', 500);
        }
    }

    /**
     * Generate available time slots
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
                $start = $now->copy()->addMinutes(15)->startOfMinute(); // 15 min buffer from now
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

    /**
     * Generate meeting link based on event type
     *
     * @param EventType $eventType
     * @return string|null
     */
    private function generateMeetingLink(EventType $eventType): ?string
    {
        switch ($eventType->location_type) {
            case 'zoom':
                // TODO: Integrate with Zoom API
                return 'https://zoom.us/j/placeholder';
            case 'google_meet':
                // TODO: Integrate with Google Meet API
                return 'https://meet.google.com/placeholder';
            case 'custom':
                return $eventType->location_details;
            default:
                return null;
        }
    }

    /**
     * Cancel a booking (public endpoint)
     *
     * @param Request $request
     * @param Booking $booking
     * @return JsonResponse
     */
    public function cancelBooking(Request $request, Booking $booking): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'nullable|string|max:500',
                'attendee_email' => 'required|email'
            ]);

            // Verify attendee email matches
            if ($booking->attendee_email !== $validated['attendee_email']) {
                return $this->forbiddenResponse('Invalid attendee email');
            }

            if ($booking->status === 'cancelled') {
                return $this->errorResponse('Booking is already cancelled', 422);
            }

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now()
            ]);

            // TODO: Send cancellation email
            // TODO: Remove from calendar

            return $this->successResponse(
                new BookingResource($booking->fresh()),
                'Booking cancelled successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to cancel booking', 500);
        }
    }

    /**
     * Get event types for a user
     */
    public function getEventTypes(string $username): JsonResponse
    {
        try {
            $user = User::where('username', $username)->first();

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $eventTypes = EventType::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('duration')
                ->get();

            return $this->successResponse(
                EventTypeResource::collection($eventTypes),
                'Event types retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve event types', 500);
        }
    }

    /**
     * Confirm a booking (for bookings that require confirmation)
     */
    public function confirmBooking(Booking $booking): JsonResponse
    {
        try {
            if ($booking->status !== 'pending') {
                return $this->errorResponse('Booking is not pending confirmation', 422);
            }

            $booking->update(['status' => 'scheduled']);

            return $this->successResponse(
                new BookingResource($booking->fresh()->load('eventType')),
                'Booking confirmed successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to confirm booking', 500);
        }
    }
    public function rescheduleBooking(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'attendee_email' => 'required|email',
        ]);

        if ($booking->attendee_email !== $validated['attendee_email']) {
            return back()->withErrors(['attendee_email' => 'The provided email does not match the booking.'])->withInput();
        }

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($booking->eventType->duration);

        $booking->update([
            'booking_date' => $startTime->toDateString(),
            'start_time' => $startTime->toTimeString(),
            'end_time' => $endTime->toTimeString(),
        ]);

        return redirect()->route('public.booking.reschedule', $booking)
            ->with('success', 'Your booking has been successfully rescheduled.');
    }
}
