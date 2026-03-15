<?php

namespace App\Http\Resources\V1\Task;

use App\Models\V1\ActivityLog;
use App\Models\V1\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function collect;

/**
 * @mixin ActivityLog
 */
class TaskActivityResource extends JsonResource
{
    private function transformKeys(?array $values): ?array
    {
        if (!$values) return null;

        return collect($values)
            ->mapWithKeys(fn($value, $key) => [str($key)->camel()->toString() => $value])
            ->toArray();
    }


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event' => $this->event,
            'user' => $this->user ? [
                'id' => $this->user->uuid,
                'name' => $this->user->name,
            ] : null,
            'oldValues' => $this->transformKeys($this->old_values),
            'newValues' => $this->transformKeys($this->new_values),
            'createdAt' => $this->created_at,
        ];
    }
}
