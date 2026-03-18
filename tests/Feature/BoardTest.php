<?php

use App\Constants\Routes;
use App\Enums\V1\MembershipRoleEnum;
use App\Enums\V1\ProjectStatusEnum;
use App\Enums\V1\ProjectVisibilityEnum;
use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Models\V1\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create();
    $this->organization->members()->attach($this->user->id, [
        'role' => MembershipRoleEnum::ADMIN,
        'joined_at' => now(),
    ]);

    $this->project = Project::factory()->create(['organization_id' => $this->organization->id]);
});

describe('creates a board', function () {
    it('creates a default board on project creation', function () {
        $this->actingAs($this->user);
        $response = $this->postJson(route(Routes::PROJECT_STORE, $this->organization->uuid), [
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(20),
            'status' => fake()->randomElement(ProjectStatusEnum::cases())->value,
            'visibility' => fake()->randomElement(ProjectVisibilityEnum::cases())->value,
            'startDate' => $startDate = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'endDate' => fake()->dateTimeBetween($startDate, '+1 year')->format('Y-m-d'),
        ]);

        $project = Project::where('uuid', $response->json('data.id'))->value('id');
        $this->assertDatabaseHas('boards', ['project_id' => $project]);
    });

    it('successfully creates a board', function () {
        $this->actingAs($this->user);
        $response = $this->postJson(route(Routes::BOARD_STORE, $this->project->uuid), [
            'name' => 'Test board',
            'description' => 'Test board description'
        ])->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'isDefault',
                    'createdAt'
                ]
            ]);

        $this->assertDatabaseHas('boards', ['uuid' => $response->json('data.id')]);
    });

    it('fails to create with not provided name', function () {
        $this->actingAs($this->user);
        $this->postJson(route(Routes::BOARD_STORE, $this->project->uuid), [
            'description' => 'Test board description'
        ])->assertStatus(422);
    });

    it('fails to create if not authenticated', function () {
        $this->postJson(route(Routes::BOARD_STORE, $this->project->uuid), [
            'name' => 'Test board',
            'description' => 'Test board description'
        ])->assertStatus(401);
    });

    it('fails to create if user do not belong to org', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route(Routes::BOARD_STORE, $this->project->uuid), [
            'name' => 'Test board',
            'description' => 'Test board description'
        ])->assertStatus(403);
    });
});
