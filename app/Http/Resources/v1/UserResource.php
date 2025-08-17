<?php
// app/Http/Resources/UserResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'bio' => $this->bio,
            'timezone' => $this->timezone,
            'avatar' => $this->avatar,
            'booking_url' => $this->booking_url,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}