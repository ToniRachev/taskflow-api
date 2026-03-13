<?php

namespace App\Http\Resources\V1\Task;

use App\Models\V1\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class BaseTaskResource extends JsonResource
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
            'reference' => $this->reference,
            'title' => $this->title,
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,
            'dueDate' => $this->due_date,
            'assignee' => $this->assignee ? [
                'id' => $this->assignee->uuid,
                'name' => $this->assignee->name,
                'avatarUrl' => $this->assignee->profile->avatar_url,
            ] : null,
            'createdAt' => $this->created_at,
        ];
    }
}
