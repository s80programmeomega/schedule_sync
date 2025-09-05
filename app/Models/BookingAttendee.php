<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * BookingAttendee Model
 *
 * Manages multiple attendees for bookings.
 */
class BookingAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'attendee_id',
        'attendee_type',
        'name',
        'email',
        'phone',
        'role',
        'status',
        'responded_at',
        'response_notes',
        'email_notifications',
        'sms_notifications',
        'reminder_24h_sent_at',
        'reminder_1h_sent_at'
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'reminder_24h_sent_at' => 'datetime',
            'reminder_1h_sent_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function attendee(): MorphTo
    {
        return $this->morphTo();
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    public function hasResponded(): bool
    {
        return !is_null($this->responded_at);
    }
}