<?php

namespace App\Http\Controllers\V1;

use App\Constants\Message;
use App\Filters\V1\TaskFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Task\StoreTaskRequest;
use App\Http\Requests\V1\Task\UpdateTaskAssigneeRequest;
use App\Http\Requests\V1\Task\UpdateTaskBulkStatusRequest;
use App\Http\Requests\V1\Task\UpdateTaskPriorityRequest;
use App\Http\Requests\V1\Task\UpdateTaskRequest;
use App\Http\Requests\V1\Task\UpdateTaskStatusRequest;
use App\Http\Resources\V1\Task\BaseTaskResource;
use App\Http\Resources\V1\Task\CreatedTaskResource;
use App\Http\Resources\V1\Task\TaskActivityResource;
use App\Http\Resources\V1\Task\TaskResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Models\V1\Task;
use App\Services\V1\TaskService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function array_merge;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService)
    {
    }


    public function index(Organization $organization, Project $project, TaskFilter $filters)
    {
        $this->authorize('viewAny', [Task::class, $organization]);
        $tasks = Task::filter($filters)->paginate($filters->getPerPage())->appends(request()->query());
        return ApiResponse::withPagination($tasks, BaseTaskResource::class);
    }

    public function store(Organization $organization, Project $project, StoreTaskRequest $request)
    {
        $this->authorize('create', [Task::class, $organization]);

        $task = $this->taskService->createTask(
            $request->validated(),
            $request->user()->id,
            $project);

        return ApiResponse::created(
            Message::TASK_CREATED,
            CreatedTaskResource::make($task)
        );
    }

    public function show(Organization $organization, Project $project, Task $task)
    {
        $this->authorize('view', [Task::class, $organization]);
        return ApiResponse::ok(data: TaskResource::make($task));
    }

    public function update(
        Organization      $organization,
        Project           $project,
        Task              $task,
        UpdateTaskRequest $request
    )
    {
        $this->authorize('update', [$task, $organization]);
        return ApiResponse::ok(
            data: TaskResource::make($this->taskService
                ->updateTask(
                    $request->validated(), $task
                ))
        );
    }

    public function destroy(
        Organization $organization,
        Project      $project,
        Task         $task,
    )
    {
        $this->authorize('delete', [$task, $organization]);
        $task->delete();
        return ApiResponse::noContent();
    }

    public function updateStatus(
        Organization            $organization,
        Project                 $project,
        Task                    $task,
        UpdateTaskStatusRequest $request
    )
    {
        $this->authorize('update', [$task, $organization]);
        return ApiResponse::ok(
            data: TaskResource::make($this->taskService
                ->updateStatus(
                    $request->validated('status'),
                    $task
                ))
        );
    }

    public function updateAssignee(
        Organization              $organization,
        Project                   $project,
        Task                      $task,
        UpdateTaskAssigneeRequest $request
    )
    {
        $this->authorize('assign', [$task, $organization]);
        return ApiResponse::ok(
            data: TaskResource::make($this->taskService
                ->updateAssignee(
                    $request->validated('assignee_id'),
                    $task,
                ))
        );
    }

    public function updatePriority(
        Organization              $organization,
        Project                   $project,
        Task                      $task,
        UpdateTaskPriorityRequest $request
    )
    {
        $this->authorize('update', [$task, $organization]);
        return ApiResponse::ok(
            data: TaskResource::make($this->taskService
                ->updatePriority(
                    $request->validated('priority'),
                    $task
                ))
        );
    }

    public function indexSubtask(
        Organization $organization,
        Project      $project,
        Task         $task
    )
    {
        $this->authorize('viewAny', [Task::class, $organization]);
        return ApiResponse::ok(data: BaseTaskResource::collection($task->subtasks));
    }

    public function storeSubtask(
        Organization     $organization,
        Project          $project,
        Task             $task,
        StoreTaskRequest $request
    )
    {
        $this->authorize('create', [Task::class, $organization]);
        $data = array_merge($request->validated(), ['parent_id' => $task->id]);
        return ApiResponse::created(data: CreatedTaskResource::make($this->taskService->createTask($data, $request->user()->id, $project)));
    }

    public function updateBulkStatus(
        Organization                $organization,
        Project                     $project,
        UpdateTaskBulkStatusRequest $request
    )
    {
        $this->authorize('updateBulkStatus', [Task::class, $organization]);
        return ApiResponse::ok(
            data: $this->taskService
                ->updateBulkStatus(
                    $request->validated(),
                    $project
                )
        );
    }

    public function indexActivity(
        Organization $organization,
        Project      $project,
        string       $taskId
    )
    {
        $this->authorize('view', [Task::class, $organization]);
        $task = Task::where('uuid', $taskId)->withTrashed()->firstOrFail();
        return ApiResponse::withPagination($task->activity()->latest()->paginate(10), TaskActivityResource::class);
    }
}
