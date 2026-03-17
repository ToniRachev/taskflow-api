<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function asset;


class MemberResource extends JsonResource
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
            'avatarUrl' => $this->whenLoaded('profile', fn() =>
                $this->profile->avatar_url
                    ? asset('/storage' . '/' . $this->profile->avatar_url)
                    : null),
            'role' => $this->pivot->role,
            'joinedAt' => $this->pivot->joined_at,
        ];
    }
}
