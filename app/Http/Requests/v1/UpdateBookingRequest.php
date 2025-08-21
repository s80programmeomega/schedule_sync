<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && $this->booking->user_id === auth()->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'event_type_id' => 'required|exists:event_types,id',
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_notes' => 'nullable|string|max:1000',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            // 'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:scheduled,completed,cancelled,no_show,pending',
            'meeting_link' => 'nullable|url|max:255',
            'cancellation_reason' => 'nullable|string|max:500',
        ];
    }
}
