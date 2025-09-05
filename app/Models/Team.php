<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Team Model
 *
 * Represents a team/organization in the system. Teams can have multiple members
 * with different roles and permissions. Similar to Calendly's team workspace.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $logo
 * @property string $timezone
 * @property array|null $settings
 * @property int $owner_id
 * @property bool $is_active
 * @property string $plan
 * @property int $max_members
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'timezone',
        'settings',
        'owner_id',
        'is_active',
        'plan',
        'max_members',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
            'max_members' => 'integer',
        ];
    }

    /**
     * Boot the model.
     * Automatically generate slug when creating a team.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name);

                // Ensure slug uniqueness
                $originalSlug = $team->slug;
                $counter = 1;
                while (static::where('slug', $team->slug)->exists()) {
                    $team->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        // When a team is created, automatically add the owner as a team member
        static::created(function ($team) {
            $team->members()->create([
                'user_id' => $team->owner_id,
                'role' => 'owner',
                'status' => 'active',
                'joined_at' => now(),
            ]);
        });
    }

    /**
     * Get the team owner.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all team members.
     */
    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get active team members only.
     */
    public function activeMembers(): HasMany
    {
        return $this->members()->where('status', 'active');
    }

    /**
     * Get team members with their user data.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot(['role', 'status', 'joined_at', 'permissions'])
            ->withTimestamps()
            ->wherePivot('status', 'active');
    }

    /**
     * Get team event types.
     */
    public function eventTypes(): HasMany
    {
        return $this->hasMany(EventType::class);
    }

    /**
     * Get team contacts.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get team groups.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get team bookings (through event types).
     */
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, EventType::class);
    }

    /**
     * Check if user is a member of this team.
     */
    public function hasMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Check if user has a specific role in this team.
     */
    public function userHasRole(User $user, string $role): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->where('role', $role)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Check if user can perform an action in this team.
     */
    public function userCan(User $user, string $permission): bool
    {
        $member = $this->members()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$member) {
            return false;
        }

        // Define role-based permissions
        $rolePermissions = [
            'owner' => ['*'], // All permissions
            'admin' => [
                'manage_team_settings',
                'manage_members',
                'manage_event_types',
                'manage_bookings',
                'view_analytics',
                'manage_contacts',
                'manage_groups'
            ],
            'member' => [
                'create_event_types',
                'manage_own_bookings',
                'view_team_calendar',
                'manage_contacts',
                'view_groups'
            ],
            'viewer' => [
                'view_team_calendar',
                'view_bookings'
            ]
        ];

        $userPermissions = $rolePermissions[$member->role] ?? [];

        // Check if user has all permissions (owner)
        if (in_array('*', $userPermissions)) {
            return true;
        }

        // Check specific permission
        return in_array($permission, $userPermissions);
    }

    /**
     * Get team's public booking URL.
     */
    public function getBookingUrlAttribute(): string
    {
        return url("/team/{$this->slug}");
    }

    /**
     * Check if team can add more members.
     */
    public function canAddMembers(): bool
    {
        return $this->activeMembers()->count() < $this->max_members;
    }

    /**
     * Get team statistics.
     */
    public function getStats(): array
    {
        return [
            'total_members' => $this->activeMembers()->count(),
            'total_event_types' => $this->eventTypes()->where('is_active', true)->count(),
            'total_bookings' => $this->bookings()->count(),
            'total_contacts' => $this->contacts()->where('is_active', true)->count(),
            'total_groups' => $this->groups()->where('is_active', true)->count(),
        ];
    }
}