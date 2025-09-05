<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Contact Model
 *
 * Represents external contacts who can be invited to meetings.
 * These are not team members but can participate in bookings.
 */
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'job_title',
        'notes',
        'custom_fields',
        'avatar',
        'timezone',
        'team_id',
        'created_by',
        'is_active',
        'email_notifications',
        'sms_notifications',
        'last_contacted_at',
        'total_bookings'
    ];

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'last_contacted_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function groups(): MorphToMany
    {
        return $this->morphToMany(Group::class, 'member', 'group_members')
            ->withPivot(['role', 'joined_at', 'added_by'])
            ->withTimestamps();
    }

    public function bookingAttendances(): MorphMany
    {
        return $this->morphMany(BookingAttendee::class, 'attendee');
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/avatars/' . $this->avatar)
            : "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=4f46e5&color=fff";
    }
}