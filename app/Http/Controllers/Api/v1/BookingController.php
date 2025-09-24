<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\v1\BookingResource;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Availability;
use App\Http\Resources\v1\UserResource;
use App\Http\Resources\v1\EventTypeResource;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApproved;
use App\Mail\BookingRejected;

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


    /**
     * Get user profile and available event types (only if public)
     *
     * @param string $username
     * @return JsonResponse
     */
    public function getUserProfile(string $username): JsonResponse
    {
        try {
            $user = User::where('username', $username)->first();

            if (!$user || !$user->allowsPublicBookings()) {
                return $this->notFoundResponse('User not found or not available for public booking');
            }

            $eventTypes = $user->publicEventTypes()->orderBy('duration')->get();

            return $this->successResponse([
                'user' => new UserResource($user),
                'event_types' => EventTypeResource::collection($eventTypes),
                'booking_url' => $user->public_booking_url,
            ], 'User profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user profile', 500);
        }
    }

    /**
     * Get available time slots for an event type (only if public)
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

            if (
                !$user ||
                $eventType->user_id !== $user->id ||
                !$eventType->isPubliclyBookable()
            ) {
                return $this->notFoundResponse('Event type not available for public booking');
            }

            $date = Carbon::parse($validated['date']);

            // Get user's availability for this date
            $availability = Availability::where('user_id', $user->id)
                ->whereDate('availability_date', $date)
                ->where('is_available', true)
                ->orderBy('start_time')
                ->get();

            if ($availability->isEmpty()) {
                return $this->successResponse([
                    'available_slots' => [],
                    'message' => 'No availability for this date'
                ]);
            }

            // Get existing bookings for this date
            $existingBookings = Booking::where('user_id', $user->id)
                ->whereDate('booking_date', $date)
                ->whereIn('status', ['scheduled', 'pending'])
                ->whereIn('approval_status', ['approved', 'pending'])
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
                'event_type' => new EventTypeResource($eventType),
                'booking_workflow' => $eventType->getBookingWorkflow(),
            ], 'Available slots retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve availability', 500);
        }
    }

    /**
     * Create a new booking request
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
                'attendee_phone' => 'nullable|string|max:20',
                'attendee_notes' => 'nullable|string|max:1000',
                'booking_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required',
                'timezone' => 'nullable|string'
            ]);

            $user = User::where('username', $username)->first();

            if (
                !$user ||
                $eventType->user_id !== $user->id ||
                !$eventType->isPubliclyBookable()
            ) {
                return $this->notFoundResponse('Event type not available for public booking');
            }

            $startTime = Carbon::parse($validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($eventType->duration);

            // Check for conflicts (including pending approvals)
            $conflict = Booking::where('user_id', $user->id)
                ->whereIn('status', ['scheduled', 'pending'])
                ->whereIn('approval_status', ['approved', 'pending'])
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

            // Determine initial status based on event type settings
            $initialStatus = 'pending';
            $approvalStatus = $eventType->requires_approval ? 'pending' : 'approved';

            if (!$eventType->requires_approval && !$eventType->requires_confirmation) {
                $initialStatus = 'scheduled';
            }

            // Create booking request
            $booking = Booking::create([
                'event_type_id' => $eventType->id,
                'user_id' => $user->id,
                'attendee_name' => $validated['attendee_name'],
                'attendee_email' => $validated['attendee_email'],
                'attendee_phone' => $validated['attendee_phone'] ?? null,
                'attendee_notes' => $validated['attendee_notes'],
                'booking_date' => $validated['booking_date'],
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'status' => $initialStatus,
                'approval_status' => $approvalStatus,
                'meeting_link' => $this->generateMeetingLink($eventType)
            ]);

            // Send appropriate notifications
            if ($eventType->requires_approval) {
                // Notify host about new booking request
                // TODO: Send notification to host
                $message = 'Booking request submitted successfully. You will receive an email once the host reviews your request.';
            } else {
                // Auto-approve and send confirmation
                $booking->update(['approved_at' => now()]);
                Mail::to($validated['attendee_email'])->send(new BookingApproved($booking));
                $message = 'Booking confirmed successfully. Check your email for details.';
            }

            return $this->successResponse(
                new BookingResource($booking->load('eventType')),
                $message,
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create booking request', 500);
        }
    }

    /**
     * Approve a booking request (host only)
     *
     * @param Request $request
     * @param Booking $booking
     * @return JsonResponse
     */
    public function approveBooking(Request $request, Booking $booking): JsonResponse
    {
        try {
            // Verify the request is from the host
            $user = $request->user();
            if (!$user || $booking->user_id !== $user->id) {
                return $this->forbiddenResponse('Unauthorized to approve this booking');
            }

            if (!$booking->isPendingApproval()) {
                return $this->errorResponse('Booking is not pending approval', 422);
            }

            // Approve the booking
            $booking->approve();

            // Send approval email to attendee
            Mail::to($booking->attendee_email)->send(new BookingApproved($booking));

            return $this->successResponse(
                new BookingResource($booking->fresh()->load('eventType')),
                'Booking approved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to approve booking', 500);
        }
    }

    /**
     * Reject a booking request (host only)
     *
     * @param Request $request
     * @param Booking $booking
     * @return JsonResponse
     */
    public function rejectBooking(Request $request, Booking $booking): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'nullable|string|max:500'
            ]);

            // Verify the request is from the host
            $user = $request->user();
            if (!$user || $booking->user_id !== $user->id) {
                return $this->forbiddenResponse('Unauthorized to reject this booking');
            }

            if (!$booking->isPendingApproval()) {
                return $this->errorResponse('Booking is not pending approval', 422);
            }

            // Reject the booking
            $booking->reject($validated['rejection_reason']);

            // Send rejection email to attendee
            Mail::to($booking->attendee_email)->send(new BookingRejected($booking));

            return $this->successResponse(
                new BookingResource($booking->fresh()->load('eventType')),
                'Booking rejected successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reject booking', 500);
        }
    }

    /**
     * Get pending booking requests for the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPendingBookings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $pendingBookings = Booking::where('user_id', $user->id)
                ->where('approval_status', 'pending')
                ->with(['eventType'])
                ->orderBy('booking_date')
                ->orderBy('start_time')
                ->get();

            return $this->successResponse(
                BookingResource::collection($pendingBookings),
                'Pending bookings retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve pending bookings', 500);
        }
    }
}
