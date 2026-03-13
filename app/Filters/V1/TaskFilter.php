<?php

namespace App\Filters\V1;

use App\Models\V1\User;
use function array_key_exists;

class TaskFilter extends QueryFilter
{
    protected array $multiValueFilters = [
        'status',
        'priority',
        'assignee',
        'type'
    ];

    protected array $allowerFilters = [
        'status',
        'sortBy',
        'priority',
        'assignee',
        'type'
    ];

    private array $mappedSortBy = [
        'newest' => ['column' => 'created_at', 'value' => 'desc'],
        'oldest' => ['column' => 'created_at', 'value' => 'asc']
    ];

    public function status(array $query): void
    {
        $this->builder->whereIn('status', $query);
    }

    public function sortBy($query): void
    {
        if (array_key_exists($query, $this->mappedSortBy)) {
            $data = $this->mappedSortBy[$query];
            $this->builder->orderBy($data['column'], $data['value']);
        }
    }

    public function priority($priority): void
    {
        $this->builder->whereIn('priority', $priority);
    }

    public function assignee($uuids): void
    {
        $ids = User::whereIn('uuid', $uuids)->pluck('id');
        $this->builder->whereIn('assignee_id', $ids);
    }

    public function type($types): void
    {
        $this->builder->whereIn('type', $types);
    }
}
