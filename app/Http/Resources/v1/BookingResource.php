<?php
// app/Http/Resources/BookingResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_type' => new EventTypeResource($this->whenLoaded('eventType')),
            'attendee_name' => $this->attendee_name,
            'attendee_email' => $this->attendee_email,
            'attendee_notes' => $this->attendee_notes,
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'status' => $this->status,
            'meeting_link' => $this->meeting_link,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'is_upcoming' => $this->is_upcoming,
            'time_until' => $this->time_until,
            'formatted_date_time' => $this->formatted_date_time,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
