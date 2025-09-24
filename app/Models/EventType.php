<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * EventType Model
 * Represents different types of appointments a user can offer
 */
class EventType extends Model
{
    /** @use HasFactory<\Database\Factories\EventTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'duration',
        'location_type',
        'location_details',
        'buffer_time_before',
        'buffer_time_after',
        'is_active',
        'requires_confirmation',
        'requires_approval',
        'max_events_per_day',
        'color',
        'is_team_event',
        'allow_multiple_attendees',
        'max_attendees',
        'assignment_method',
    ];

    protected function casts(): array
    {
        return [
            'is_team_event' => 'boolean',
            'allow_multiple_attendees' => 'boolean',
            'max_attendees' => 'integer',
            'assignment_method' => 'string',
            'requires_approval' => 'boolean',
        ];
    }

    /**
     * Get the user who owns this event type
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team this event type belongs to
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get all bookings for this event type
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get public booking URL for this event type
     */
    public function getBookingUrlAttribute()
    {
        return url("/{$this->user->username}/{$this->id}");
    }

    /**
     * Get formatted duration string
     */
    public function getFormattedDurationAttribute()
    {
        return $this->duration . ' min';
    }

    /**
     * Get default colors for event types
     */
    public static function getDefaultColors()
    {
        return [
            '#5D5CDE',
            '#FF5733',
            '#33FF57',
            '#3357FF',
            '#FF33A1',
            '#33FFF5',
            '#FFD700',
            '#8A2BE2'
        ];
    }

    /**
     * Get location types
     */
    public static function getLocationTypes()
    {
        return [
            'zoom' => 'Zoom',
            'google_meet' => 'Google Meet',
            'phone' => 'Phone Call',
            'whatsapp' => 'WhatsApp',
            'custom' => 'Custom'
        ];
    }


    /**
     * Check if this event type is available for public booking
     *
     * @return bool
     */
    public function isPubliclyBookable(): bool
    {
        return $this->is_active &&
            $this->user->allowsPublicBookings();
    }

    /**
     * Get the booking workflow status for this event type
     *
     * @return string
     */
    public function getBookingWorkflow(): string
    {
        if ($this->requires_approval) {
            return 'approval_required';
        }

        if ($this->requires_confirmation) {
            return 'confirmation_required';
        }

        return 'instant_booking';
    }
}
