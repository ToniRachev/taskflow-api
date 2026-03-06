<?php

namespace App\Http\Controllers\V1;

use App\Constants\Message;
use App\Enums\OrganizationMembershipRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Organization\StoreOrganizationRequest;
use App\Http\Resources\V1\MemberResource;
use App\Http\Resources\V1\OrganizationResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Organization;
use Illuminate\Http\JsonResponse;
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

    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = DB::transaction(function () use ($request) {
            $organization = Organization::create([
                'name' => $request->validated('name'),
                'slug' => $this->generateUniqueSlug($request->validated('name')),
            ]);

            if ($request->hasFile('logo')) {
                $path = $request->validated('logo')->store('organizations/logos', 'public');
                $organization->forceFill(['logo_url' => $path])->save();
            }

            $organization->members()->attach($request->user()->id, [
                'role' => OrganizationMembershipRoleEnum::OWNER->value,
                'joined_at' => now(),
            ]);

            return $organization;
        });

        return ApiResponse::created(Message::ORGANIZATION_CREATED, new OrganizationResource($organization));
    }

    public function index(Organization $organization)
    {
        return ApiResponse::ok(data: MemberResource::collection($organization->members));
    }
}
