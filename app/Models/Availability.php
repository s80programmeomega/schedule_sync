<?php

namespace App\Models;

use App\Services\TimeSlotService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Availability Model
 * Represents a user's available time slots for each day of the week
 */
class Availability extends Model
{
    /** @use HasFactory<\Database\Factories\AvailabilityFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'availability_date',
        // 'day_of_week',
        'timezone_id',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'availability_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Get the user this availability belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    // Accessors to format time fields
    public function getStartTimeAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }

    public function bookings()
    {
        return $this
            ->hasMany(Booking::class, 'user_id', 'user_id')
            ->whereDate('booking_date', $this->availability_date)
            ->where('status', 'scheduled')
            ->where(function ($query) {
                $query
                    ->whereBetween('start_time', [$this->getRawOriginal('start_time'), $this->getRawOriginal('end_time')])
                    ->orWhereBetween('end_time', [$this->getRawOriginal('start_time'), $this->getRawOriginal('end_time')]);
            });
    }

    public function getBookingCountAttribute()
    {
        return $this->bookings()->get()->count();
    }

    public function getTimeSlots($duration = 30)
    {
        return app(TimeSlotService::class)->generateTimeSlots($this, $duration);
    }

    /**
     * Check if this availability is publicly visible for booking
     */
    public function isPubliclyVisible(): bool
    {
        return $this->is_available &&
            $this->user->allowsPublicBookings() &&
            $this->availability_date >= now()->toDateString();
    }

    /**
     * Get available time slots for public booking
     */
    public function getPublicTimeSlots($duration = 30): array
    {
        if (!$this->isPubliclyVisible()) {
            return [];
        }

        return $this->getTimeSlots($duration);
    }
}
