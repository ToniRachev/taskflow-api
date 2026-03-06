<?php

namespace App\Http\Resources\V1;

use App\Models\V1\OrganizationMembership;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OrganizationMembership
 */

class OrganizationMembershipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'userId' => $this->user_id,
            'organizationId' => $this->organization_id,
            'role' => $this->role,
            'joined_at' => $this->joined_at
        ];
    }
}
