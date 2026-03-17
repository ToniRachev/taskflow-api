<?php

namespace App\Http\Controllers\V1;

use App\Constants\Message;
use App\Filters\V1\TaskFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Task\TaskStoreRequest;
use App\Http\Requests\V1\Task\TaskAssigneeUpdateRequest;
use App\Http\Requests\V1\Task\TaskBulkStatusUpdateRequest;
use App\Http\Requests\V1\Task\TaskPriorityUpdateRequest;
use App\Http\Requests\V1\Task\TaskUpdateRequest;
use App\Http\Requests\V1\Task\TaskStatusUpdateRequest;
use App\Http\Resources\V1\TaskActivityResource;
use App\Http\Resources\V1\TaskResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Project;
use App\Models\V1\Task;
use App\Services\V1\TaskService;
use function array_merge;
use function request;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    public function index(Project $project, TaskFilter $filters)
    {
        $this->authorize('viewAny', [Task::class, $project]);
        $tasks = Task::filter($filters)
            ->with('assignee.profile')
            ->latest()
            ->paginate($filters->getPerPage())
            ->appends(request()->query());
        return ApiResponse::withPagination($tasks, TaskResource::class);
    }

    public function store(Project $project, TaskStoreRequest $request)
    {
        $this->authorize('create', [Task::class, $project]);

        $task = $this->taskService->createTask(
            $request->validated(),
            $request->user()->id,
            $project)
            ->load([
                'assignee.profile',
                'parent',
                'reporter'
            ]);

        return ApiResponse::created(
            Message::TASK_CREATED,
            TaskResource::created($task)
        );
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load([
            'assignee',
            'reporter',
            'parent',
            'parent',
            'activity' => fn($query) => $query->with('user')->latest()->limit(10),
        ])->loadCount('subtasks');

        return ApiResponse::ok(data: TaskResource::detailed($task));
    }

    public function update(
        Task              $task,
        TaskUpdateRequest $request
    )
    {
        $this->authorize('update', [$task]);
        $task = ($this->taskService
            ->updateTask(
                $request->validated(), $task
            ))->load([
            'assignee',
            'reporter',
            'parent',
            'parent',
            'activity' => fn($query) => $query->with('user')->latest()->limit(10),
        ])->loadCount('subtasks');
        return ApiResponse::ok(
            data: TaskResource::detailed($task)
        );
    }

    public function destroy(
        Task $task,
    )
    {
        $this->authorize('delete', [$task]);
        $task->delete();
        return ApiResponse::noContent();
    }

    public function updateStatus(
        Task                    $task,
        TaskStatusUpdateRequest $request
    )
    {
        $this->authorize('update', [$task]);
        $task = $this->taskService
            ->updateStatus(
                $request->validated('status'),
                $task
            )->load([
                'assignee',
                'reporter',
                'parent',
                'parent',
                'activity' => fn($query) => $query->with('user')->latest()->limit(10),
            ])->loadCount('subtasks');
        return ApiResponse::ok(data: TaskResource::detailed($task));
    }

    public function updateAssignee(
        Task                      $task,
        TaskAssigneeUpdateRequest $request
    )
    {
        $this->authorize('assign', [$task]);
        $task = ($this->taskService
            ->updateAssignee(
                $request->validated('assignee_id'),
                $task,
            ))->load([
            'assignee',
            'reporter',
            'parent',
            'parent',
            'activity' => fn($query) => $query->with('user')->latest()->limit(10),
        ])->loadCount('subtasks');
        return ApiResponse::ok(data: TaskResource::detailed($task));
    }

    public function updatePriority(
        Task                      $task,
        TaskPriorityUpdateRequest $request
    )
    {

        $this->authorize('update', [$task]);

        $task = $this->taskService
            ->updatePriority(
                $request->validated('priority'),
                $task)->load([
                'assignee',
                'reporter',
                'parent',
                'parent',
                'activity' => fn($query) => $query->with('user')->latest()->limit(10),
            ])->loadCount('subtasks');
        return ApiResponse::ok(
            data: TaskResource::detailed($task));
    }

    public function indexSubtask(
        Task $task
    )
    {
        $this->authorize('view', [$task]);

        $subtasks = $task->subtasks()->with('assignee.profile')
            ->latest()
            ->paginate();

        return ApiResponse::withPagination($subtasks, TaskResource::class);
    }

    public function storeSubtask(
        Task             $task,
        TaskStoreRequest $request
    )
    {
        $this->authorize('createSubtask', [$task]);
        $data = array_merge($request->validated(), ['parent_id' => $task->id]);
        $task = $this->taskService->createSubtask(
            $data,
            $task,
            $request->user()->id)
            ->load([
                'assignee.profile',
                'parent',
                'reporter'
            ]);

        return ApiResponse::created(
            data: TaskResource::created($task));
    }

    public function updateBulkStatus(
        Project                     $project,
        TaskBulkStatusUpdateRequest $request
    )
    {
        $this->authorize('updateBulkStatus', [Task::class, $project]);
        return ApiResponse::ok(
            data: $this->taskService
                ->updateBulkStatus(
                    $request->validated(),
                    $project
                )
        );
    }

    public function indexActivity(
        string $taskId
    )
    {
        $task = Task::where('uuid', $taskId)->withTrashed()->firstOrFail();
        $this->authorize('view', [Task::class, $task]);
        return ApiResponse::withPagination($task->activity()->latest()->paginate(10), TaskActivityResource::class);
    }
}
