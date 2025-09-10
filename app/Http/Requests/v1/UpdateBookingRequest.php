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
            'booking_date' => 'required|date|after_or_equal:today',
            'timezone_id' => 'required|exists:timezones,id',
            'start_time' => 'required',
            'status' => 'required|in:scheduled,completed,cancelled,no_show,pending',
            'meeting_link' => 'nullable|url|max:255',
            'cancellation_reason' => 'nullable|string|max:500',
        ];
    }
}
