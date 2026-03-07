<?php

namespace App\Http\Resources\V1\Organization;

use App\Models\V1\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Organization
 */
class OrganizationResource extends JsonResource
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
            'slug' => $this->slug,
            'logoUrl' => $this->logo_url ? asset('storage/' . $this->logo_url) : null,
            'plan' => $this->plan,
            'isActive' => $this->is_active,
            'role' => $this->whenPivotLoaded('organization_memberships', fn() => $this->pivot->role),
            'createdAt' => $this->created_at,
        ];
    }
}
