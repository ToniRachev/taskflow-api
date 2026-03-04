<?php

namespace App\Http\Resources\V1;

use App\Models\V1\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Profile
 */

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bio' => $this->bio,
            'phone' => $this->phone,
            'github_url' => $this->github_url,
            'preferences' => $this->preferences,
        ];
    }
}
