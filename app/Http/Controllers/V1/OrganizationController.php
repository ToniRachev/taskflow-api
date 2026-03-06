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
use App\Services\V1\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        return ApiResponse::created(Message::ORGANIZATION_CREATED, new OrganizationResource($organization));
    }

    public function index(Organization $organization)
    {
        return ApiResponse::ok(data: MemberResource::collection($organization->members));
    }
}
