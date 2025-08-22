<?php

namespace App\Http\Requests\v1;

use App\Models\Availability;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        // must be authenticated and be the creator of an availability to be able to update it
        return auth()->check() && $this->route('availability')->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'availability_date' => 'required|date|after_or_equal:today',
            'timezone_id' => 'required|exists:timezones,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'is_available' => 'boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasOverlappingAvailability()) {
                $validator->errors()->add('start_time', 'This time slot overlaps with existing availability.');
            }
        });
    }

    private function hasOverlappingAvailability(): bool
    {
        return Availability::where('user_id', auth()->id())
            ->where('availability_date', $this->availability_date)
            ->where('id', '!=', $this->route('availability')->id)
            ->where(function ($query) {
                $query->whereBetween('start_time', [$this->start_time, $this->end_time])
                    ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                    ->orWhere(function ($q) {
                        $q->where('start_time', '<=', $this->start_time)
                            ->where('end_time', '>=', $this->end_time);
                    });
            })
            ->exists();
    }
}
