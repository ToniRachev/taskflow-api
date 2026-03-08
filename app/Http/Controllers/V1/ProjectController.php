<?php

namespace App\Http\Controllers\V1;

use App\Enums\V1\ProjectStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Project\StoreProjectRequest;
use App\Http\Requests\V1\Project\UpdateProjectRequest;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Services\V1\ProjectService;
use Illuminate\Http\Request;

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
        return Project::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Organization $organization, StoreProjectRequest $request)
    {
        $this->authorize('create', [Project::class, $organization]);
        return $this->projectService->createProject(
            $request->validated(),
            $request->user()->id,
            $organization->id
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Project $project)
    {
        $this->authorize('view', [Project::class, $project]);
        return $project;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Organization $organization, Project $project)
    {
        $this->authorize('update', $project);
        $this->projectService->updateProject($request->validated(), $project);
        return $project;
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
