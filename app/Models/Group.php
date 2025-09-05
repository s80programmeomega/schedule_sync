<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Group Model
 *
 * Organizes team members and contacts into logical collections.
 */
class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'type',
        'team_id',
        'created_by',
        'is_active',
        'settings'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }


    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teamMembers(): MorphToMany
    {
        return $this->morphedByMany(TeamMember::class, 'member', 'group_members')
            ->withPivot(['role', 'joined_at', 'added_by'])
            ->withTimestamps();
    }

    public function contacts(): MorphToMany
    {
        return $this->morphedByMany(Contact::class, 'member', 'group_members')
            ->withPivot(['role', 'joined_at', 'added_by'])
            ->withTimestamps();
    }

    public function getMemberCountAttribute(): int
    {
        return $this->teamMembers()->count() + $this->contacts()->count();
    }
}