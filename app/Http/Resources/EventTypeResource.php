<?php
// app/Http/Resources/EventTypeResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Event Type API Resource
 *
 * Transforms EventType model data into a consistent API response format.
 *
 * Resource Pattern Benefits:
 * 1. Consistent data structure across all API endpoints
 * 2. Easy to modify response format without changing controllers
 * 3. Can include/exclude fields based on context
 * 4. Handles relationships and computed properties elegantly
 *
 * Real-world example: Similar to how Stripe formats their API responses
 * with consistent field naming and structure.
 */
class EventTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'duration' => $this->duration,
            'formatted_duration' => $this->formatted_duration,
            'location_type' => $this->location_type,
            'location_details' => $this->location_details,
            'buffer_time_before' => $this->buffer_time_before,
            'buffer_time_after' => $this->buffer_time_after,
            'is_active' => $this->is_active,
            'requires_confirmation' => $this->requires_confirmation,
            'max_events_per_day' => $this->max_events_per_day,
            'color' => $this->color,
            'booking_url' => $this->booking_url,
            'bookings_count' => $this->when(isset($this->bookings_count), $this->bookings_count),
            'recent_bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
