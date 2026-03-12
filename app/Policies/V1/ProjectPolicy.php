<?php

namespace App\Policies\V1;

use App\Enums\MembershipRoleEnum;
use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Models\V1\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
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
    public function view(User $user, Project $project): bool
    {
        return $project->organization->isMember($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $organization->hasAdminAccess($user);
    }

    /**p
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->organization->hasAdminAccess($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->organization->hasAdminAccess($user);
    }

    public function archive(User $user, Project $project) {
        return $project->organization->hasAdminAccess($user);
    }
}
