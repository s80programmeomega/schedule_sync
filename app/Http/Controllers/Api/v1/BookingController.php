<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\v1\BookingResource;
use Carbon\Carbon;

/**
 * Booking Management
 *
 * Handles appointment scheduling and management.
 *
 * Booking Strategies:
 * 1. Instant Booking (Chosen): Immediate confirmation
 * 2. Approval Required: Host must approve
 * 3. Payment Required: Payment before confirmation
 *
 * Real-world: Similar to how Zoom Scheduler handles meetings
 */
class BookingController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Booking::with(['eventType'])
                ->where('user_id', $request->user()->id);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('from_date')) {
                $query->where('start_time', '>=', $request->input('from_date'));
            }

            if ($request->has('to_date')) {
                $query->where('start_time', '<=', $request->input('to_date'));
            }

            $bookings = $query->latest('start_time')->paginate(15);

            return $this->successResponse(
                BookingResource::collection($bookings)->response()->getData(),
                'Bookings retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve bookings', 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'event_type_id' => 'required|exists:event_types,id',
                'attendee_name' => 'required|string|max:255',
                'attendee_email' => 'required|email|max:255',
                'attendee_notes' => 'nullable|string|max:1000',
                'start_time' => 'required|date|after:now',
                'meeting_link' => 'nullable|url|max:255',
            ]);

            $eventType = EventType::findOrFail($validated['event_type_id']);

            if ($eventType->user_id !== $request->user()->id) {
                return $this->forbiddenResponse('You cannot create bookings for this event type');
            }

            // Calculate end time
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($eventType->duration);

            // Check for conflicts
            $conflict = Booking::where('user_id', $request->user()->id)
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
                return $this->errorResponse('Time slot conflicts with existing booking', 422);
            }

            $validated['user_id'] = $request->user()->id;
            $validated['end_time'] = $endTime;
            $validated['status'] = $eventType->requires_confirmation ? 'pending' : 'scheduled';

            $booking = Booking::create($validated);

            return $this->successResponse(
                new BookingResource($booking->load('eventType')),
                'Booking created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create booking', 500);
        }
    }

    public function show(Booking $booking): JsonResponse
    {
        if ($booking->user_id !== auth()->id()) {
            return $this->forbiddenResponse();
        }

        return $this->successResponse(
            new BookingResource($booking->load('eventType')),
            'Booking retrieved successfully'
        );
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        try {
            if ($booking->user_id !== auth()->id()) {
                return $this->forbiddenResponse();
            }

            if ($booking->status === 'cancelled') {
                return $this->errorResponse('Booking is already cancelled', 422);
            }

            $validated = $request->validate([
                'cancellation_reason' => 'nullable|string|max:500',
            ]);

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now(),
            ]);

            return $this->successResponse(
                new BookingResource($booking->fresh()),
                'Booking cancelled successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to cancel booking', 500);
        }
    }

    public function reschedule(Request $request, Booking $booking): JsonResponse
    {
        try {
            if ($booking->user_id !== auth()->id()) {
                return $this->forbiddenResponse();
            }

            $validated = $request->validate([
                'start_time' => 'required|date|after:now',
            ]);

            $startTime = Carbon::parse($validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($booking->eventType->duration);

            $booking->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);

            return $this->successResponse(
                new BookingResource($booking->fresh()),
                'Booking rescheduled successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reschedule booking', 500);
        }
    }
}
