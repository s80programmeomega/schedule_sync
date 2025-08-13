<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'meeting_link' => 'nullable|url|max:255',
        ];
    }
}
