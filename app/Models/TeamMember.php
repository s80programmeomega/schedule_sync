<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

/**
 * TeamMember Model
 *
 * Represents the relationship between a user and a team,
 * including their role and permissions within the team.
 *
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property string $role
 * @property string $status
 * @property array|null $permissions
 * @property \Carbon\Carbon|null $joined_at
 * @property \Carbon\Carbon $invited_at
 * @property string|null $invitation_token
 * @property \Carbon\Carbon|null $invitation_expires_at
 * @property int|null $invited_by
 */
class TeamMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'status',
        'permissions',
        'joined_at',
        'invited_at',
        'invitation_token',
        'invitation_expires_at',
        'invited_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'joined_at' => 'datetime',
            'invited_at' => 'datetime',
            'invitation_expires_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate invitation token when creating pending members
        static::creating(function ($teamMember) {
            if ($teamMember->status === 'pending' && empty($teamMember->invitation_token)) {
                $teamMember->invitation_token = Str::random(64);
                $teamMember->invitation_expires_at = now()->addDays(7); // 7 days to accept
            }
        });
    }

    /**
     * Get the team this member belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user associated with this team member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who invited this member.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get groups this team member belongs to.
     */
    public function groups(): MorphToMany
    {
        return $this->morphToMany(Group::class, 'member', 'group_members')
            ->withPivot(['role', 'joined_at', 'added_by'])
            ->withTimestamps();
    }

    /**
     * Check if invitation is still valid.
     */
    public function isInvitationValid(): bool
    {
        return $this->status === 'pending'
            && $this->invitation_expires_at
            && $this->invitation_expires_at->isFuture();
    }

    /**
     * Accept the team invitation.
     */
    public function acceptInvitation(): bool
    {
        if (!$this->isInvitationValid()) {
            return false;
        }

        $this->update([
            'status' => 'active',
            'joined_at' => now(),
            'invitation_token' => null,
            'invitation_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Decline the team invitation.
     */
    public function declineInvitation(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->delete();
        return true;
    }

    /**
     * Check if member has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->team->userCan($this->user, $permission);
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayAttribute(): string
    {
        return match ($this->role) {
            'owner' => 'Owner',
            'admin' => 'Administrator',
            'member' => 'Member',
            'viewer' => 'Viewer',
            default => ucfirst($this->role)
        };
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending Invitation',
            default => ucfirst($this->status)
        };
    }

    /**
     * Scope to get active members only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get pending members only.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get members by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}