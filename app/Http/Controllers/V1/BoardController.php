<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Board\BoardStoreRequest;
use App\Http\Requests\V1\Board\BoardUpdateRequest;
use App\Http\Resources\V1\BoardResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Board;
use App\Models\V1\Project;

class BoardController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('viewAny', [Board::class, $project]);
        $boards = $project->boards()->withCount('columns')->get();
        return ApiResponse::ok(data: BoardResource::collection($boards));
    }

    public function show(Board $board)
    {
        $this->authorize('view', $board);
        $board->load('columns')->get();
        return ApiResponse::ok(data: BoardResource::detailed($board));
    }

    public function store(Project $project, BoardStoreRequest $request)
    {
        $this->authorize('create', [Board::class, $project]);
        $board = $project->boards()->create($request->validated());
        return ApiResponse::created(data: BoardResource::make($board));
    }

    public function update(Board $board, BoardUpdateRequest $request)
    {
        $this->authorize('update', $board);
        $board->fill($request->validated());

        if ($board->isDirty()) {
            $board->save();
        }

        return ApiResponse::ok(data: BoardResource::make($board));
    }

    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);
        $board->delete();
        return ApiResponse::noContent();
    }
}
