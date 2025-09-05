<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Notifications\EmailVerificationNotification;

/**
 * User Model with Email Verification
 *
 * This model implements MustVerifyEmail contract which provides:
 * - hasVerifiedEmail() method to check verification status
 * - markEmailAsVerified() method to mark email as verified
 * - sendEmailVerificationNotification() method to send verification emails
 * - getEmailForVerification() method to get the email for verification
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'timezone_id',
        'bio',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all event types for this user
     */
    public function eventTypes()
    {
        return $this->hasMany(EventType::class);
    }

    /**
     * Get all bookings for this user's events
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get user's availability schedule
     */
    public function availability()
    {
        return $this->hasMany(Availability::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * Get public booking URL
     */
    public function getBookingUrlAttribute()
    {
        return url("/{$this->username}");
    }

    /**
     * Determine if the user's email address has been verified.
     * This method is provided by MustVerifyEmail contract
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     * This method is provided by MustVerifyEmail contract
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     * This method is provided by MustVerifyEmail contract
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification);
    }

    /**
     * Get the email address that should be used for verification.
     * This method is provided by MustVerifyEmail contract
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Get teams where user is a member
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot(['role', 'status', 'joined_at', 'permissions'])
            ->withTimestamps()
            ->wherePivot('status', 'active');
    }

    /**
     * Get user's default team
     */
    public function defaultTeam()
    {
        return $this->belongsTo(Team::class, 'default_team_id');
    }

    /**
     * Get team memberships
     */
    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get contacts created by this user
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'created_by');
    }

    /**
     * Check if user is member of a team
     */
    public function isMemberOf(Team $team): bool
    {
        return $this->teams()->where('teams.id', $team->id)->exists();
    }

    /**
     * Get user's role in a team
     */
    public function getRoleInTeam(Team $team): ?string
    {
        $membership = $this->teamMemberships()
            ->where('team_id', $team->id)
            ->where('status', 'active')
            ->first();

        return $membership?->role;
    }
}
