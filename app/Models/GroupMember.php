<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'member_id',
        'member_type',
        'role',
        'joined_at',
        'added_by',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function member(): MorphTo
    {
        return $this->morphTo();
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function getMemberNameAttribute(): string
    {
        return match ($this->member_type) {
            'App\Models\TeamMember' => $this->member->user->name,
            'App\Models\Contact' => $this->member->name,
            default => 'Unknown'
        };
    }

    public function getMemberEmailAttribute(): string
    {
        return match ($this->member_type) {
            'App\Models\TeamMember' => $this->member->user->email,
            'App\Models\Contact' => $this->member->email,
            default => ''
        };
    }
}
