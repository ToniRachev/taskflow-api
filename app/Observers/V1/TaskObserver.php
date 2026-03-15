<?php

namespace App\Observers\V1;

use App\Enums\V1\ActivityLogEventEnum;
use App\Models\V1\ActivityLog;
use App\Models\V1\Task;
use function auth;
use function collect;
use function request;

class TaskObserver
{
    private array $internalFields = [
        'id',
        'order',
        'parent_id',
        'project_id',
        'assignee_id',
        'reporter_id',
        'reference_number'
    ];

    private function filterFields(?array $fields): array
    {
        return collect($fields)
            ->except($this->internalFields)
            ->toArray();
    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        ActivityLog::create([
            'loggable_type' => Task::class,
            'loggable_id' => $task->id,
            'user_id' => auth()->id(),
            'event' => ActivityLogEventEnum::CREATED->value,
            'old_values' => null,
            'new_values' => $this->filterFields($task->getAttributes()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        ActivityLog::create([
            'loggable_type' => Task::class,
            'loggable_id' => $task->id,
            'user_id' => auth()->id(),
            'event' => ActivityLogEventEnum::UPDATED->value,
            'old_values' => $this->filterFields($task->getOriginal()),
            'new_values' => $this->filterFields($task->getChanges()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        ActivityLog::create([
            'loggable_type' => Task::class,
            'loggable_id' => $task->id,
            'user_id' => auth()->id(),
            'event' => ActivityLogEventEnum::DELETED->value,
            'old_values' => $this->filterFields($task->getOriginal()),
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
