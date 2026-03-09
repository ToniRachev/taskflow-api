<?php

namespace App\Services\V1;

use App\Models\V1\Project;

class ProjectService
{
    private function generateUniqueKey($name, $organizationId): string
    {

        $chunks = explode(' ', $name);

        if (count($chunks) === 1) {
            return substr(strtoupper($chunks[0]), 0, 10);
        } else {
            $key = collect($chunks)
                ->take(10)
                ->map(fn($chunk) => strtoupper($chunk[0]))
                ->implode('');

            $suffix = Project::where('key', 'LIKE', $key . '%')
                    ->where('organization_id', $organizationId)
                    ->count() + 1;

            return $key . ($suffix === 1 ? '' : $suffix);
        }
    }

    public function createProject($data, $userId, $organizationId): Project
    {
        $project = new Project($data);

        if (!array_key_exists('key', $data)) {
            $project['key'] = $this->generateUniqueKey($data['name'], $organizationId);
        }

        $project['owner_id'] = $userId;
        $project['organization_id'] = $organizationId;
        $project->save();

        return $project;
    }

    public function updateProject($data, Project $project): Project
    {
        $project->fill($data);

        if ($project->isDirty()) {
            $project->save();
        }

        return $project;
    }
}
