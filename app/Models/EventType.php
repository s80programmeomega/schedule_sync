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
        'max_events_per_day',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'requires_confirmation' => 'boolean',
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
}
