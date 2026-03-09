<?php

namespace App\Http\Controllers\V1;

use App\Constants\Message;
use App\Enums\V1\ProjectStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Project\StoreProjectRequest;
use App\Http\Requests\V1\Project\UpdateProjectRequest;
use App\Http\Resources\V1\Project\ProjectCreatedResource;
use App\Http\Resources\V1\Project\ProjectListResource;
use App\Http\Resources\V1\Project\ProjectResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Services\V1\ProjectService;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService)
    {
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        $this->authorize('viewAny', [Project::class, $organization]);
        return ApiResponse::ok(
            data: ProjectListResource::collection(Project::all())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Organization $organization, StoreProjectRequest $request)
    {
        $this->authorize('create', [Project::class, $organization]);
        $project = $this->projectService->createProject(
            $request->validated(),
            $request->user()->id,
            $organization->id
        );

        return ApiResponse::created(
            Message::PROJECT_CREATED,
            ProjectCreatedResource::make($project)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Project $project)
    {
        $this->authorize('view', [Project::class, $project]);
        return ApiResponse::ok(data: ProjectResource::make($project));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Organization $organization, Project $project)
    {
        $this->authorize('update', $project);
        $this->projectService->updateProject($request->validated(), $project);
        return ApiResponse::ok(data: ProjectResource::make($project));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return ApiResponse::noContent();
    }

    public function archiveProject(Organization $organization, Project $project)
    {
        $this->authorize('archive', $project);
        if ($project->status !== ProjectStatusEnum::ARCHIVED) {
            $project->forceFill(['status' => ProjectStatusEnum::ARCHIVED])
                ->save();
        }

        return ApiResponse::ok();
    }
}
