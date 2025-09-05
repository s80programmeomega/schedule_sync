<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo' => $this->logo,
            'timezone' => $this->timezone,
            'is_active' => $this->is_active,
            'plan' => $this->plan,
            'max_members' => $this->max_members,
            'booking_url' => $this->booking_url,
            'owner' => $this->whenLoaded('owner', fn() => [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
            ]),
            'member_count' => $this->whenLoaded('activeMembers', fn() => $this->activeMembers->count()),
            'user_role' => $this->when(auth()->check(), fn() => auth()->user()->getRoleInTeam($this->resource)),
            'stats' => $this->when($request->routeIs('*.show'), fn() => $this->getStats()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
