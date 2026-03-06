<?php

namespace App\Http\Controllers\V1;

use App\Enums\OrganizationMembershipRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Organization\StoreOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\V1\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    private function generateUniqueSlug($name): string
    {
        $slug = Str::slug($name);
        if (Organization::where('slug', $slug)->exists()) {
            return $this->generateUniqueSlug($name . '-' . Str::random(6));
        }

        return $slug;
    }

    public function store(StoreOrganizationRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $organization = Organization::create([
                'name' => $request->name,
                'slug' => $this->generateUniqueSlug($request->name),
            ]);

            if ($request->file()) {
                $path = $request->validated('logo')->store('organizations/logos', 'public');
                $organization->forceFill(['logo_url' => $path])->save();
            }

            $organization->members()->attach($request->user()->id, [
                'role' => OrganizationMembershipRoleEnum::OWNER->value,
                'joined_at' => now(),
            ]);

            return new OrganizationResource($organization);
        });
    }
}
