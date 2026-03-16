<?php

namespace App\Http\Controllers\V1;

use App\Constants\Message;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Organization\StoreOrganizationRequest;
use App\Http\Requests\V1\Organization\UpdateOrganizationRequest;
use App\Http\Resources\V1\MemberResource;
use App\Http\Resources\V1\OrganizationResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\Organization;
use App\Services\V1\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct(private readonly OrganizationService $organizationService)
    {
    }


    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = $this->organizationService
            ->createOrganization(
                $request->validated(),
                $request->user()->id,
                $request->validated('logo')
            );
        return ApiResponse::created(Message::ORGANIZATION_CREATED, OrganizationResource::make($organization));
    }

    public function members(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);
        return ApiResponse::ok(data: MemberResource::collection($organization->members));
    }

    public function index(Request $request): JsonResponse
    {
        return ApiResponse::ok(data: OrganizationResource::collection($request->user()->organizations));
    }

    public function show(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);
        return ApiResponse::ok(data: OrganizationResource::make($organization->loadCount('members')));
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);
        $updatedOrganization = $this->organizationService
            ->updateOrganization(
                $organization,
                $request->validated(),
                $request->hasFile('logo')
            );

        return ApiResponse::ok(data: OrganizationResource::make($updatedOrganization));
    }

    public function destroy(Organization $organization): JsonResponse
    {
        $this->authorize('destroy', $organization);
        $organization->delete();
        return ApiResponse::noContent();
    }
}
