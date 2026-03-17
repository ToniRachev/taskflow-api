<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Enums\V1\ProjectStatusEnum;
use App\Enums\V1\ProjectVisibilityEnum;
use App\Models\V1\Organization;

beforeEach(function () {
    $this->user = \App\Models\V1\User::factory()->create();
    $this->organization = Organization::factory()->create();
    $this->payload = [
        'uuid' => fake()->uuid(),
        'name' => fake()->sentence(3),
        'key' => 'PROJECT',
        'description' => fake()->sentence(20),
        'status' => fake()->randomElement(ProjectStatusEnum::cases())->value,
        'visibility' => fake()->randomElement(ProjectVisibilityEnum::cases())->value,
        'startDate' => $startDate = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        'endDate' => fake()->dateTimeBetween($startDate, '+1 year')->format('Y-m-d'),
    ];
});

describe('creates a project', function () {
    it('successfully creates a project', function () {

        $this->organization->members()->attach($this->user->id, [
            'role' => \App\Enums\V1\MembershipRoleEnum::OWNER->value,
            'joined_at' => now(),
        ]);

        $this->actingAs($this->user);
        $response = $this->postJson(route(\App\Constants\Routes::PROJECT_STORE, $this->organization->uuid), $this->payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'key',
                    'status',
                    'visibility',
                    'startDate',
                    'endDate',
                    'createdAt',
                    'updatedAt',
                    'description'
                ]
            ]);

        expect(
            $this->organization->projects()->where('uuid', $response->json('data.id'))->exists()
        )->toBeTrue();
        $this->assertDatabaseHas('projects', ['uuid' => $response->json('data.id')]);
    });

    it('fails to create if user is not organization admin', function () {
        $this->organization->members()->attach($this->user->id, [
            'role' => \App\Enums\V1\MembershipRoleEnum::MEMBER,
            'joined_at' => now()
        ]);

        $this->actingAs($this->user);

        $this->postJson(route(\App\Constants\Routes::PROJECT_STORE, $this->organization->uuid), $this->payload)
            ->assertStatus(403);
    });
});

describe('updates project', function () {
    it('updates successfully project', function () {
        $this->organization->members()->attach($this->user->id, [
            'role' => \App\Enums\V1\MembershipRoleEnum::OWNER,
            'joined_at' => now()
        ]);

        $this->actingAs($this->user);
        $project = \App\Models\V1\Project::factory()->create([
            'owner_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $this->patchJson(route(\App\Constants\Routes::PROJECT_UPDATE, [$project->uuid]), [
            'name' => 'new project name',
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'new project name');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'new project name'
        ]);
    });

    it('fails to update if not admin or owner', function () {
        $this->actingAs($this->user);
        $project = \App\Models\V1\Project::factory()->create([
            'owner_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $this->patchJson(route(\App\Constants\Routes::PROJECT_UPDATE, [$project->uuid]), [
            'name' => 'new project name',
        ])
            ->assertStatus(403);
    });
});

describe('deletes project', function () {
    it('delete project successfully', function () {
        $this->organization->members()->attach($this->user->id, [
            'role' => \App\Enums\V1\MembershipRoleEnum::OWNER,
            'joined_at' => now()
        ]);

        $this->actingAs($this->user);
        $project = \App\Models\V1\Project::factory()->create([
            'owner_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $this->deleteJson(route(\App\Constants\Routes::PROJECT_DESTROY, [$project->uuid]))
            ->assertStatus(204);

        $this->assertSoftDeleted('projects', [
            'id' => $project->id
        ]);
    });

    it('fails to delete if not admin or owner', function () {
        $this->actingAs($this->user);
        $project = \App\Models\V1\Project::factory()->create([
            'owner_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $this->deleteJson(route(\App\Constants\Routes::PROJECT_DESTROY, [$project->uuid]))
            ->assertStatus(403);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
        ]);
    });
});
