<?php

namespace App\Http\Resources\V1;

use App\Http\Resources\V1\Profile\ProfileResource;
use App\Models\V1\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'lastSeenAt' => $this->last_seen_at,
        ];
    }
}
