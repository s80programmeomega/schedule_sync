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
        'attendee_name',
        'attendee_email',
        'attendee_notes',
        'booking_date',
        'start_time',
        // 'end_time',
        'status',
        'meeting_link',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            // 'start_time' => 'datetime:H:i',
            // 'end_time' => 'datetime:H:i',
            'cancelled_at' => 'datetime',
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
        return $this->full_start_time?->format('M j, Y \a\t g:i A');
    }
}
