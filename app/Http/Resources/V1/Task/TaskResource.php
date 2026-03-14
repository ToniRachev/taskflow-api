<?php

namespace App\Http\Resources\V1\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function array_merge;

class TaskResource extends CreatedTaskResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'subtaskCount' => 0,
            'commentCount' => 0,
            'parentId' => $this->parent_id,
            'activity' => [
                'event' => 'event',
                'user' => 'user',
                'oldValues' => null,
                'newValues' => null,
                'createdAt' => null,
            ],
        ]);
    }
}
