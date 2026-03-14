<?php

namespace App\Policies\V1;

use App\Models\V1\Organization;
use App\Models\V1\Task;
use App\Models\V1\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Organization $organization): bool
    {
        return $organization->isMember($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $organization->isMember($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $organization->canModify($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task, Organization $organization): bool
    {
        return $organization->hasAdminAccess($user) || $task->isReporterOrAssignee($user);
    }

    public function assign(User $user, Task $task, Organization $organization): bool
    {
        return $organization->hasAdminAccess($user) || $task->isReporter($user);
    }

    public function updateBulkStatus(User $user, Organization $organization): bool
    {
        return $organization->hasAdminAccess($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task, Organization $organization): bool
    {
        return $organization->hasAdminAccess($user) || $task->isReporter($user);
    }
}
