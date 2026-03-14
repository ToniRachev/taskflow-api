<?php

namespace App\Services\V1;

use App\Enums\V1\TaskStatusEnum;
use App\Models\V1\Project;
use App\Models\V1\Task;
use App\Models\V1\User;
use Arr;
use Illuminate\Support\Facades\DB;
use function compact;

class TaskService
{
    private function getNextReferenceNumber($project): int
    {
        return $project->tasks()->withTrashed()->lockForUpdate()->max('reference_number') + 1;
    }

    private function generateReference($key, $referenceNumber): string
    {
        return $key . '-' . $referenceNumber;
    }

    private function getNextOrder($status, $project): int
    {
        $status = $status ?? TaskStatusEnum::BACKLOG->value;
        return $project->tasks()->withTrashed()->where(['status' => $status])->lockForUpdate()->max('order') + 1;
    }

    public function createTask($data, $userId, Project $project): Task
    {
        return DB::transaction(function () use ($data, $userId, $project) {
            if (Arr::has($data, 'assignee_id')) {
                $data['assignee_id'] = User::where(['uuid' => $data['assignee_id']])->first()->id;
            }

            $referenceNumber = $this->getNextReferenceNumber($project);

            return Task::create([
                ...$data,
                'reporter_id' => $userId,
                'project_id' => $project->id,
                'reference' => $this->generateReference($project->key, $referenceNumber),
                'reference_number' => $referenceNumber,
                'order' => $this->getNextOrder($data['status'], $project),
            ]);
        });
    }

    public function updateTask($data, Task $task): Task
    {
        $task->fill($data);

        if ($task->isDirty()) {
            $task->save();
        }

        return $task;
    }

    public function updateStatus($status, Task $task): Task
    {
        $task->fill(['status' => $status]);

        if ($task->isDirty('status')) {
            $task->completed_at = $status === 'done' ? now() : null;
            $task->save();
        }

        return $task;
    }

    public function updateAssignee($assignee, Task $task): Task
    {
        if ($assignee) {
            $assignee = User::where('uuid', $assignee)->value('id');
        }

        $task->fill(['assignee_id' => $assignee]);

        if ($task->isDirty('assignee_id')) {
            $task->save();
        }
        return $task;
    }

    public function updatePriority($priority, Task $task): Task
    {
        $task->fill(['priority' => $priority]);

        if ($task->isDirty('priority')) {
            $task->save();
        }

        return $task;
    }

    public function updateBulkStatus($data, Project $project): array
    {
        $taskIds = $data['task_ids'];
        $updated = $project->tasks()
            ->whereIn(
                'uuid', $taskIds
            )
            ->update(['status' => $data['status']]);

        return compact('updated', 'taskIds');
    }
}
