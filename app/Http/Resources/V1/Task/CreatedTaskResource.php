<?php

namespace App\Http\Resources\V1\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function array_merge;

class CreatedTaskResource extends BaseTaskResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'description' => $this->description,
            'storyPoints' => $this->story_points,
            'order' => $this->order,
            'completedAt' => $this->completed_at,
            'reporter' => [
                'id' => $this->reporter->uuid,
                'name' => $this->reporter->name,
            ],
            'updatedAt' => $this->updated_at,
        ]);
    }
}
