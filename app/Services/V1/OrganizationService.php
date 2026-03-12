<?php

namespace App\Services\V1;

use App\Enums\MembershipRoleEnum;
use App\Models\V1\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrganizationService
{
    private function generateUniqueSlug($name): string
    {
        $slug = Str::slug($name);
        if (Organization::withTrashed()->where('slug', $slug)->exists()) {
            return $this->generateUniqueSlug($name . '-' . Str::random(6));
        }

        return $slug;
    }

    public function createOrganization($data, $userId, $file): Organization
    {
        return DB::transaction(function () use ($data, $userId, $file) {
            $organization = Organization::create([
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug($data['name'])
            ]);

            if ($file) {
                $path = $file->store('organizations/logos', 'public');
                $organization->forceFill(['logo_url' => $path])->save();
            }

            $organization->members()->attach($userId, [
                'role' => MembershipRoleEnum::OWNER->value,
                'joined_at' => now(),
            ]);

            return $organization;
        });
    }

    public function updateOrganization(Organization $organization, $data, $hasLogo): Organization
    {
        $organization->fill($data);

        if ($organization->isDirty('name')) {
            $organization->slug = $this->generateUniqueSlug($data['name']);
        }

        if ($hasLogo) {
            if ($organization->logo_url) {
                Storage::disk('public')->delete($organization->logo_url);
            }

            $organization->logo_url = $data['logo']->store('organizations/logos', 'public');
        }

        if ($organization->isDirty()) {
            $organization->save();
        }

        return $organization;
    }
}
