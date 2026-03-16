<?php

namespace App\Http\Resources\V1\Task;

use App\Http\Resources\V1\BaseResource;
use App\Models\V1\Task;
use Illuminate\Http\Request;

/**
 * @mixin Task
 */
class TaskResource extends BaseResource
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
            'reference' => $this->reference,
            'title' => $this->title,
            'description' => $this->when($this->detailed || $this->created, $this->description),
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,
            'storyPoints' => $this->when($this->detailed || $this->created, $this->story_points),
            'order' => $this->when($this->detailed || $this->created, $this->order),
            'dueDate' => $this->due_date,
            'completedAt' => $this->when($this->detailed || $this->created, $this->completed_at),
            'assignee' => $this->whenLoaded('assignee', fn() => [
                'id' => $this->assignee->uuid,
                'name' => $this->assignee->name,
                'avatarUrl' => $this->assignee->profile?->avatar_url,
            ]),
            'reporter' => $this->whenLoaded('reporter', fn() => [
                'id' => $this->reporter->uuid,
                'name' => $this->reporter->name,
            ]),
            'subtaskCount' => $this->when($this->detailed, $this->subtasks_count),
            'activity' => $this->when($this->detailed, fn() => $this->whenLoaded('activity', fn() => TaskActivityResource::collection($this->activity)
            )
            ),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->when($this->detailed || $this->created, $this->updated_at),
        ];
    }
}
