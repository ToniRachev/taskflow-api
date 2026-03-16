<?php

namespace App\Policies\V1;

use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Models\V1\Task;
use App\Models\V1\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Project $project): bool
    {
        return $project->organization->isMember($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $task->project->organization->isMember($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        return $project->organization->canModify($user);
    }

    public function createSubtask(User $user, Task $task): bool
    {
        return $task->project->organization->canModify($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $task->project->organization->hasAdminAccess($user) || $task->isReporterOrAssignee($user);
    }

    public function assign(User $user, Task $task): bool
    {
        return $task->project->organization->hasAdminAccess($user) || $task->isReporter($user);
    }

    public function updateBulkStatus(User $user,Project $project): bool
    {
        return $project->organization->hasAdminAccess($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->project->organization->hasAdminAccess($user) || $task->isReporter($user);
    }
}
