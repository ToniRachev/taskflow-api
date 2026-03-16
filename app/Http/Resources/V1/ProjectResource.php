<?php

namespace App\Http\Resources\V1;

use App\Models\V1\Project;
use Illuminate\Http\Request;

/**
 * @mixin Project
 */
class ProjectResource extends BaseResource
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
            'description' => $this->when($this->created || $this->detailed, $this->description),
            'status' => $this->status,
            'visibility' => $this->visibility,
            'taskCount' => $this->when(!$this->detailed, $this->whenCounted('tasks')),
            'stats' => $this->when($this->detailed, fn() => $this->whenCounted('tasks', fn() => [
                'totalTasks' => $this->tasks_count,
                'completedTasks' => $this->completed_tasks_count,
                'inProgressTasks' => $this->in_progress_tasks_count,
                'backlogTasks' => $this->backlog_tasks_count,
                'inReviewTasks' => $this->in_review_tasks_count,
                'todoTasks' => $this->todo_tasks_count,
            ])),
            'startDate' => $this->when($this->created || $this->detailed, $this->start_date),
            'endDate' => $this->end_date,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->when($this->created || $this->detailed, $this->updated_at),
        ];
    }
}
