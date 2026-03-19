<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Column\ColumnStoreRequest;
use App\Http\Resources\V1\ColumnResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Board;
use App\Models\V1\Column;

class ColumnController extends Controller
{
    public function store(Board $board, ColumnStoreRequest $request)
    {
        $board->load('project.organization.members');
        $this->authorize('create', [Column::class, $board]);
        $column = $board->columns()->create($request->validated());
        return ApiResponse::created(data: ColumnResource::make($column));
    }
}
