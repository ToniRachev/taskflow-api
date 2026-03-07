<?php

namespace App\Policies\V1;

use App\Enums\OrganizationMembershipRoleEnum;
use App\Models\V1\Organization;
use App\Models\V1\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    public function view(User $user, Organization $organization): bool
    {
        return $organization->members()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Organization $organization): bool
    {
        $member = $organization->members->where('id', $user->id)->first();
        if ($member) {
            $role = $member->pivot->role;

            return $role === OrganizationMembershipRoleEnum::ADMIN->value || $role === OrganizationMembershipRoleEnum::OWNER->value;
        }

        return false;
    }
}
