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
            'preferences' => [
                'theme' => $this->preferences['theme'],
                'language' => $this->preferences['language'],
                'notifications' => [
                    'email' => $this->preferences['notifications']['email'],
                    'mentioned' => $this->preferences['notifications']['mentioned'],
                    'inApp' => $this->preferences['notifications']['in_app'],
                    'taskAssigned' => $this->preferences['notifications']['task_assigned'],
                    'dueSoon' => $this->preferences['notifications']['due_soon'],
                ]
            ]
        ];
    }
}
