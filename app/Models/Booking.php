<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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
        'start_time',
        'end_time',
        'status',
        'meeting_link',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
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

    /**
     * Check if booking is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->start_time->isFuture() && $this->status === 'scheduled';
    }

    /**
     * Get time until booking starts
     */
    public function getTimeUntilAttribute()
    {
        if (!$this->is_upcoming) {
            return null;
        }

        return $this->start_time->diffForHumans();
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->start_time->format('M j, Y \a\t g:i A');
    }
}
