<?php

namespace App\Http\Resources\V1\Organization;

use Illuminate\Http\Request;

class OrganizationDetailResource extends OrganizationResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'membersCount' => $this->whenCounted('members'),
            'updatedAt' => $this->updated_at,
        ]);
    }
}
