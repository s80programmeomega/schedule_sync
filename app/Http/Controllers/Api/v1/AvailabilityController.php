<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Availability Management
 *
 * Handles user's weekly availability schedule.
 *
 * Time Management Strategies:
 * 1. Fixed Schedule (Chosen): Set weekly recurring availability
 * 2. Dynamic Schedule: Set availability for specific dates
 * 3. Hybrid: Combination of both with overrides
 *
 * Real-world: Similar to how doctors set their clinic hours
 */
class AvailabilityController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        try {
            $availability = Availability::where('user_id', $request->user()->id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');

            return $this->successResponse($availability, 'Availability retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve availability', 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'is_available' => 'boolean',
            ]);

            $validated['user_id'] = $request->user()->id;

            // Check for overlapping availability
            $overlap = Availability::where('user_id', $request->user()->id)
                ->where('day_of_week', $validated['day_of_week'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('start_time', '<=', $validated['start_time'])
                                ->where('end_time', '>=', $validated['end_time']);
                        });
                })->exists();

            if ($overlap) {
                return $this->errorResponse('Time slot overlaps with existing availability', 422);
            }

            $availability = Availability::create($validated);

            return $this->successResponse($availability, 'Availability created successfully', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create availability', 500);
        }
    }

    public function show(Availability $availability): JsonResponse
    {
        if ($availability->user_id !== auth()->user->id) {
            return $this->forbiddenResponse();
        }

        return $this->successResponse($availability, 'Availability retrieved successfully');
    }

    public function update(Request $request, Availability $availability): JsonResponse
    {
        try {
            if ($availability->user_id !== auth()->user->id) {
                return $this->forbiddenResponse();
            }

            $validated = $request->validate([
                'day_of_week' => 'sometimes|required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'start_time' => 'sometimes|required|date_format:H:i',
                'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
                'is_available' => 'boolean',
            ]);

            $availability->update($validated);

            return $this->successResponse($availability->fresh(), 'Availability updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update availability', 500);
        }
    }

    public function destroy(Availability $availability): JsonResponse
    {
        if ($availability->user_id !== auth()->user->id) {
            return $this->forbiddenResponse();
        }

        $availability->delete();
        return $this->successResponse(null, 'Availability deleted successfully');
    }

    public function bulkStore(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'availability' => 'required|array',
                'availability.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'availability.*.start_time' => 'required|date_format:H:i',
                'availability.*.end_time' => 'required|date_format:H:i|after:availability.*.start_time',
                'availability.*.is_available' => 'boolean',
            ]);

            $created = [];
            foreach ($validated['availability'] as $slot) {
                $slot['user_id'] = $request->user()->id;
                $created[] = Availability::create($slot);
            }

            return $this->successResponse($created, 'Bulk availability created successfully', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create bulk availability', 500);
        }
    }
}
