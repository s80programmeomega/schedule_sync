<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Services\TimeSlotService;


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

        return $this->hasMany(Booking::class, 'user_id', 'user_id')
            ->whereDate('booking_date', $this->availability_date)
            ->where(function ($query) {
                $query->whereBetween('start_time', [$this->getRawOriginal('start_time'), $this->getRawOriginal('end_time')])
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

}
