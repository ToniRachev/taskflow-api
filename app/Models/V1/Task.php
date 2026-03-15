<?php

namespace App\Models\V1;

use App\Enums\V1\TaskPriorityEnum;
use App\Enums\V1\TaskStatusEnum;
use App\Enums\V1\TaskTypeEnum;
use App\Filters\V1\TaskFilter;
use App\Observers\V1\TaskObserver;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    use HasUuid, HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'assignee_id',
        'reporter_id',
        'parent_id',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'story_points',
        'order',
        'due_date',
        'completed_at',
        'reference',
        'reference_number'
    ];

    protected $attributes = [
        'type' => TaskTypeEnum::TASK->value,
        'status' => TaskStatusEnum::BACKLOG->value,
        'priority' => TaskPriorityEnum::MEDIUM->value,
        'order' => 0
    ];

    protected function casts(): array
    {
        return [
            'type' => TaskTypeEnum::class,
            'status' => TaskStatusEnum::class,
            'priority' => TaskPriorityEnum::class,
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function scopeFilter(Builder $builder, TaskFilter $filters): Builder
    {
        return $filters->apply($builder);
    }

    public function isReporterOrAssignee(User $user): bool
    {
        return $this->reporter_id === $user->id || $this->assignee_id === $user->id;
    }

    public function isReporter(User $user): bool
    {
        return $this->reporter_id === $user->id;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function activity(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}
