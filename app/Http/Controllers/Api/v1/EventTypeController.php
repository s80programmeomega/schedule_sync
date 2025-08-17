<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\v1\EventTypeResource;
use App\Http\Resources\v1\EventTypeCollection;

/**
 * Event Types API Controller
 *
 * Manages different types of appointments/meetings that users can offer.
 *
 * Design Patterns Used:
 *
 * 1. Resource Pattern (Chosen):
 *    - Pros: Consistent data transformation, easy to modify response structure
 *    - Cons: Additional layer of complexity
 *    - Use case: When you need consistent API responses across different contexts
 *
 * 2. Repository Pattern (Alternative):
 *    - Pros: Better separation of concerns, easier testing
 *    - Cons: More boilerplate code
 *    - Use case: Complex business logic, multiple data sources
 *
 * 3. Service Layer Pattern (Alternative):
 *    - Pros: Encapsulates business logic, reusable across controllers
 *    - Cons: Can become bloated
 *    - Use case: Complex operations involving multiple models
 *
 * Real-world example: Similar to how Calendly manages different meeting types
 * (15-min call, 30-min consultation, 1-hour workshop, etc.)
 */
class EventTypeController extends ApiController
{
    /**
     * Display a listing of event types
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = EventType::where('user_id', $request->user()->id)
                ->withCount('bookings');

            // Apply filters
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('location_type')) {
                $query->where('location_type', $request->input('location_type'));
            }

            // Apply sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = min($request->input('per_page', 15), 100); // Max 100 items per page
            $eventTypes = $query->paginate($perPage);

            return $this->successResponse(
                new EventTypeCollection($eventTypes),
                'Event types retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve event types', 500);
        }
    }

    /**
     * Store a newly created event type
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'duration' => 'required|integer|min:5|max:480', // 5 minutes to 8 hours
                'location_type' => 'required|in:zoom,google_meet,phone,whatsapp,custom',
                'location_details' => 'nullable|string|max:255',
                'buffer_time_before' => 'nullable|integer|min:0|max:60',
                'buffer_time_after' => 'nullable|integer|min:0|max:60',
                'requires_confirmation' => 'boolean',
                'max_events_per_day' => 'nullable|integer|min:1|max:20',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/', // Hex color validation
            ]);

            // Set default values
            $validated['user_id'] = $request->user()->id;
            $validated['is_active'] = true;
            $validated['color'] = $validated['color'] ?? EventType::getDefaultColors()[0];

            $eventType = EventType::create($validated);

            return $this->successResponse(
                new EventTypeResource($eventType),
                'Event type created successfully',
                201
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create event type', 500);
        }
    }

    /**
     * Display the specified event type
     *
     * @param EventType $eventType
     * @return JsonResponse
     */
    public function show(EventType $eventType): JsonResponse
    {
        try {
            // Check ownership
            if ($eventType->user_id !== auth()->user->id) {
                return $this->forbiddenResponse('You do not have permission to view this event type');
            }

            $eventType->load(['bookings' => function ($query) {
                $query->latest()->limit(5); // Load recent bookings
            }]);

            return $this->successResponse(
                new EventTypeResource($eventType),
                'Event type retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve event type', 500);
        }
    }

    /**
     * Update the specified event type
     *
     * @param Request $request
     * @param EventType $eventType
     * @return JsonResponse
     */
    public function update(Request $request, EventType $eventType): JsonResponse
    {
        try {
            // Check ownership
            if ($eventType->user_id !== auth()->user->id) {
                return $this->forbiddenResponse('You do not have permission to update this event type');
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'duration' => 'sometimes|required|integer|min:5|max:480',
                'location_type' => 'sometimes|required|in:zoom,google_meet,phone,whatsapp,custom',
                'location_details' => 'nullable|string|max:255',
                'buffer_time_before' => 'nullable|integer|min:0|max:60',
                'buffer_time_after' => 'nullable|integer|min:0|max:60',
                'is_active' => 'boolean',
                'requires_confirmation' => 'boolean',
                'max_events_per_day' => 'nullable|integer|min:1|max:20',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $eventType->update($validated);

            return $this->successResponse(
                new EventTypeResource($eventType->fresh()),
                'Event type updated successfully'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update event type', 500);
        }
    }

    /**
     * Remove the specified event type
     *
     * @param EventType $eventType
     * @return JsonResponse
     */
    public function destroy(EventType $eventType): JsonResponse
    {
        try {
            // Check ownership
            if ($eventType->user_id !== auth()->user->id) {
                return $this->forbiddenResponse('You do not have permission to delete this event type');
            }

            // Check if there are upcoming bookings
            $upcomingBookings = $eventType->bookings()
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')
                ->count();

            if ($upcomingBookings > 0) {
                return $this->errorResponse(
                    'Cannot delete event type with upcoming bookings. Please cancel or reschedule them first.',
                    422
                );
            }

            $eventType->delete();

            return $this->successResponse(null, 'Event type deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete event type', 500);
        }
    }

    /**
     * Toggle active status of event type
     *
     * @param EventType $eventType
     * @return JsonResponse
     */
    public function toggle(EventType $eventType): JsonResponse
    {
        try {
            // Check ownership
            if ($eventType->user_id !== auth()->user->id) {
                return $this->forbiddenResponse('You do not have permission to modify this event type');
            }

            $eventType->update(['is_active' => !$eventType->is_active]);

            $status = $eventType->is_active ? 'activated' : 'deactivated';

            return $this->successResponse(
                new EventTypeResource($eventType),
                "Event type {$status} successfully"
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to toggle event type status', 500);
        }
    }

    /**
     * Duplicate an existing event type
     *
     * @param EventType $eventType
     * @return JsonResponse
     */
    public function duplicate(EventType $eventType): JsonResponse
    {
        try {
            // Check ownership
            if ($eventType->user_id !== auth()->user->id) {
                return $this->forbiddenResponse('You do not have permission to duplicate this event type');
            }

            $duplicated = $eventType->replicate();
            $duplicated->name = $eventType->name . ' (Copy)';
            $duplicated->is_active = false; // Start as inactive
            $duplicated->save();

            return $this->successResponse(
                new EventTypeResource($duplicated),
                'Event type duplicated successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to duplicate event type', 500);
        }
    }
}