<?php

use App\Constants\Routes;
use App\Enums\V1\MembershipRoleEnum;
use App\Enums\V1\ProjectStatusEnum;
use App\Enums\V1\ProjectVisibilityEnum;
use App\Models\V1\Board;
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
    $this->board = Board::factory()->create(['project_id' => $this->project->id]);
});

//---Store---------------------------

describe('Post /boards - store', function () {
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
                    'createdAt',
                    'updatedAt'
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

//---Index------------------------------

describe('Get /boards - index', function () {
    it('index boards', function () {
        $this->actingAs($this->user);
        $this->getJson(route(Routes::BOARD_INDEX, $this->project->uuid))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'description',
                    'isDefault',
                    'columnsCount',
                    'createdAt',
                    'updatedAt',
                ]]
            ]);
    });

    it('show board', function () {
        $this->actingAs($this->user);
        $response = $this->getJson(route(Routes::BOARD_SHOW, [$this->board->uuid]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'isDefault',
                    'columns',
                    'createdAt',
                    'updatedAt',
                ]
            ]);
    });

    it('fails to list boards if not organization member', function () {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->getJson(route(Routes::BOARD_INDEX, $this->project->uuid))
            ->assertStatus(403);
    });

    it('fails view board if not organization member', function () {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->getJson(route(Routes::BOARD_SHOW, $this->board->uuid))
            ->assertStatus(403);
    });
});

//---Update----------------------------

describe('Patch /boards - update', function () {
    it('updates board', function () {
        $this->actingAs($this->user);
        $this->patchJson(route(Routes::BOARD_UPDATE, [$this->board->uuid]), [
            'name' => 'Updated name',
            'description' => 'Updated description',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated name',
                    'description' => 'Updated description'
                ]
            ]);
    });

    it('fails if empty attributes', function ($field, $value) {
        $this->actingAs($this->user);
        $this->patchJson(route(Routes::BOARD_UPDATE, [$this->board->uuid]), [
            $field => $value,
        ])->assertStatus(422);
    })->with([
        'name' => ['name', ''],
        'description' => ['description', '']
    ]);

    it('fails if duplicate name', function () {
        $this->actingAs($this->user);
        Board::factory()->create([
            'name' => 'Updated name',
            'description' => 'test',
            'project_id' => $this->project->id
        ]);

        $this->patchJson(route(Routes::BOARD_UPDATE, [$this->board->uuid]), [
            'name' => 'Updated name',
            'description' => 'Updated description',
        ])
            ->assertStatus(422);
    });

    it('fails if not authenticated', function () {
        $this->patchJson(route(Routes::BOARD_UPDATE, [$this->board->uuid]), [
            'name' => 'Updated name',
            'description' => 'Updated description',
        ])
            ->assertStatus(401);
    });

    it('fails if dont has authorization', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->patchJson(route(Routes::BOARD_UPDATE, [$this->board->uuid]), [
            'name' => 'Updated name',
            'description' => 'Updated description',
        ])
            ->assertStatus(403);
    });
});
