<?php

namespace App\Policies\V1;

use App\Enums\MembershipRoleEnum;
use App\Models\V1\Organization;
use App\Models\V1\User;

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

            return $role === MembershipRoleEnum::ADMIN->value || $role === MembershipRoleEnum::OWNER->value;
        }

        return false;
    }

    public function destroy(User $user, Organization $organization): bool
    {
        $member = $organization->members->where('id', $user->id)->first();

        if ($member && $member->pivot->role === MembershipRoleEnum::OWNER->value) {
            return true;
        }

        return false;
    }
}
