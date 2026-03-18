<?php

namespace App\Http\Resources\V1;

use App\Models\V1\Board;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Board
 */
class BoardResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'isDefault' => $this->is_default,
            'columnsCount' => $this->whenCounted('columns'),
            'columns' => $this->whenLoaded('columns', ColumnResource::collection($this->columns)),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
