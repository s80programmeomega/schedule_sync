<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'role_display' => $this->role_display,
            'status' => $this->status,
            'status_display' => $this->status_display,
            'permissions' => $this->permissions,
            'joined_at' => $this->joined_at,
            'invited_at' => $this->invited_at,
            'invitation_expires_at' => $this->invitation_expires_at,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar,
            ]),
            'team' => $this->whenLoaded('team', fn() => [
                'id' => $this->team->id,
                'name' => $this->team->name,
                'slug' => $this->team->slug,
            ]),
            'invited_by' => $this->whenLoaded('invitedBy', fn() => [
                'id' => $this->invitedBy->id,
                'name' => $this->invitedBy->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
