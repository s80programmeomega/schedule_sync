<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has(['booking_date', 'start_time', 'end_time'])) {
            $this->merge([
                'full_start_time' => Carbon::parse($this->booking_date . ' ' . $this->start_time),
                'full_end_time' => Carbon::parse($this->booking_date . ' ' . $this->end_time),
            ]);
        }
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
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            // 'end_time' => 'required|date_format:H:i|after:start_time',
            'meeting_link' => 'nullable|url|max:255',
            'full_start_time' => 'required|date|after:now',
            // 'full_end_time' => 'required|date|after:full_start_time',
        ];
    }
}
