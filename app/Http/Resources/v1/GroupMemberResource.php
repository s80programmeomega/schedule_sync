<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'joined_at' => $this->joined_at,
            'member_type' => $this->member_type,
            'member_name' => $this->member_name,
            'member_email' => $this->member_email,
            'group' => $this->whenLoaded('group', fn() => [
                'id' => $this->group->id,
                'name' => $this->group->name,
            ]),
            'member' => $this->when($this->member_type === 'App\Models\TeamMember', fn() => [
                'id' => $this->member->id,
                'user' => [
                    'id' => $this->member->user->id,
                    'name' => $this->member->user->name,
                    'email' => $this->member->user->email,
                ],
                'team_role' => $this->member->role,
            ]) ?: $this->when($this->member_type === 'App\Models\Contact', fn() => [
                'id' => $this->member->id,
                'name' => $this->member->name,
                'email' => $this->member->email,
                'company' => $this->member->company,
            ]),
            'added_by' => $this->whenLoaded('addedBy', fn() => [
                'id' => $this->addedBy->id,
                'name' => $this->addedBy->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}