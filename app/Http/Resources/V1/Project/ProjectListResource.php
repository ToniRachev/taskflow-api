<?php

namespace App\Http\Resources\V1\Project;

use App\Models\V1\Project;
use Illuminate\Http\Request;

/**
 * @mixin Project
 */
class ProjectListResource extends ProjectBaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'taskCount' => 0,
        ]);
    }
}
