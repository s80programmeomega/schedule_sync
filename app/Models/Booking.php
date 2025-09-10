<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


/**
 * Booking Model
 * Represents a scheduled appointment
 */
class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'event_type_id',
        'user_id',
        'booking_date',
        'start_time',
        'end_time',
        'timezone_id',
        'status',
        'meeting_link',
        'cancellation_reason',
        // 'cancelled_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->timezone_id) {
                $timezoneName = request()->header('X-Timezone') ??
                    request()->input('timezone') ??
                    config('app.timezone', 'UTC');

                $timezone = Timezone::where('name', $timezoneName)->first();
                $booking->timezone_id = $timezone?->id ?? Timezone::where('name', 'UTC')->first()?->id;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'cancelled_at' => 'datetime',
            'reminder_24h_sent_at' => 'datetime',
            'reminder_1h_sent_at' => 'datetime',
        ];
    }

    /**
     * Get the event type for this booking
     */
    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    /**
     * Get the user who owns this booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    // Helper method to get full datetime
    public function getFullStartTimeAttribute(): ?Carbon
    {
        if (!$this->booking_date || !$this->start_time) {
            return null;
        }
        return Carbon::parse($this->booking_date->toDateString() . ' ' . $this->start_time);
    }

    public function getFullEndTimeAttribute(): ?Carbon
    {
        if (!$this->booking_date || !$this->end_time) {
            return null;
        }
        return Carbon::parse($this->booking_date->toDateString() . ' ' . $this->end_time);
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

    public function getIsUpcomingAttribute(): bool
    {
        return $this->full_start_time?->isFuture() && $this->status === 'scheduled';
    }

    public function getTimeUntilAttribute(): ?string
    {
        if (!$this->is_upcoming) {
            return null;
        }
        return $this->full_start_time?->diffForHumans();
    }

    public function getFormattedDateTimeAttribute(): ?string
    {
        return $this->full_start_time?->format('M j, Y \a\t H:i');
    }


    /**
     * Get booking attendees
     */
    public function attendees()
    {
        return $this->hasMany(BookingAttendee::class);
    }

    /**
     * Get organizer attendee
     */
    public function organizer()
    {
        return $this->attendees()->where('role', 'organizer')->first();
    }

    /**
     * Get required attendees
     */
    public function requiredAttendees()
    {
        return $this->attendees()->where('role', 'required');
    }

    /**
     * Get optional attendees
     */
    public function optionalAttendees()
    {
        return $this->attendees()->where('role', 'optional');
    }

    /**
     * Check if booking has multiple attendees
     */
    public function hasMultipleAttendees(): bool
    {
        return $this->attendees()->count() > 1;
    }

    /**
     * Add attendee to booking
     */

    public function addAttendee($attendee, string $role = 'required', array $data = []): ?BookingAttendee
    {
        // Check if attendee already exists
        $existing = $this->attendees()
            ->where('attendee_id', $attendee->id)
            ->where('attendee_type', get_class($attendee))
            ->first();

        if ($existing) {
            return $existing;
        }

        $attendeeData = array_merge([
            'attendee_id' => $attendee->id,
            'attendee_type' => get_class($attendee),
            'name' => $attendee->name,
            'email' => $attendee->email,
            'phone' => $attendee->phone ?? null,
            'role' => $role,
            'status' => 'pending',
        ], $data);

        return $this->attendees()->create($attendeeData);
    }


    // public function addAttendee($attendee, string $role = 'required', array $data = []): BookingAttendee
    // {
    //     $attendeeData = array_merge([
    //         'attendee_id' => $attendee->id,
    //         'attendee_type' => get_class($attendee),
    //         'name' => $attendee->name,
    //         'email' => $attendee->email,
    //         'phone' => $attendee->phone ?? null,
    //         'role' => $role,
    //         'status' => 'pending',
    //     ], $data);

    //     return $this->attendees()->create($attendeeData);
    // }
}
