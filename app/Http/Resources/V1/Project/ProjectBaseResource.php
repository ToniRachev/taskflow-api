<?php

namespace App\Http\Resources\V1\Project;

use App\Models\V1\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectBaseResource extends JsonResource
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
            'key' => $this->key,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
