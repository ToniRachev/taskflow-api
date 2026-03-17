<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Board\BoardStoreRequest;
use App\Http\Resources\V1\BoardResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Project;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(Project $project)
    {
        return ApiResponse::ok(data: BoardResource::collection($project->boards));
    }

    public function store(Project $project, BoardStoreRequest $request)
    {
        $board = $project->boards()->create($request->validated());
        return ApiResponse::created(data: BoardResource::make($board));
    }
}
