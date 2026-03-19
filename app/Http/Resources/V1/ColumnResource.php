<?php

namespace App\Http\Resources\V1;

use App\Models\V1\Column;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Column
 */
class ColumnResource extends BaseResource
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
            'color' => $this->color,
            'order' => $this->order,
            'wipLimit' => $this->wip_limit,
            'tasksCount' => $this->whenCounted('tasks'),
            'tasks' => $this->whenLoaded('tasks', TaskResource::collection($this->tasks)),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
