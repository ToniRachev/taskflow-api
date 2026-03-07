<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Models\V1\User::factory()->create();
    $this->token = $this->user->createToken('auth_token')->plainTextToken;
});

describe('create organization', function () {
    it('create successfully organization', function () {
        Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg');

        $this->actingAs($this->user);
        $response = $this->post(route(\App\Constants\Routes::STORE_ORGANIZATION), [
            'name' => 'acme org',
            'logo' => $file,
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'logoUrl',
                    'plan',
                    'isActive',
                    'createdAt'
                ]
            ])
            ->assertJsonPath('data.logoUrl', fn($url) => str_contains($url, $file->hashName()));

        $this->assertDatabaseHas('organizations', ['uuid' => $response->json('data.id')]);
        Storage::disk('public')->assertExists('organizations/logos/' . $file->hashName());
    });

    it('sets the creating org user as a owner', function () {
        $this->actingAs($this->user);
        $response = $this->post(route(\App\Constants\Routes::STORE_ORGANIZATION), [
            'name' => 'acme org',
        ]);

        $organization = \App\Models\V1\Organization::where('uuid', $response->json('data.id'))->first();
        $owner = $organization->members->where('id', $this->user->id)->first();
        expect($owner->id)->toBe($this->user->id)
            ->and($owner->pivot->role)->toBe(\App\Enums\OrganizationMembershipRoleEnum::OWNER->value);
    });

    it('fails if no authorized', function () {
        $this->postJson(route(\App\Constants\Routes::STORE_ORGANIZATION), [
            'name' => 'acme org',
        ])->assertStatus(401);
    });

    it('fails with missing required field', function () {
        $this->actingAs($this->user);
        Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg');

        $this->post(route(\App\Constants\Routes::STORE_ORGANIZATION), [
            'logo' => $file,
        ])
            ->assertStatus(422);
    });
});

describe('fetch organizations', function () {
    it('gets user organizations', function () {
        $this->actingAs($this->user);
        \App\Models\V1\Organization::factory(3)->create()->each(function ($org) {
            $org->members()->attach($this->user->id, [
                'role' => fake()->randomElement(\App\Enums\OrganizationMembershipRoleEnum::cases())->value
            ]);
        });

        $this->getJson(route(\App\Constants\Routes::GET_USER_ORGANIZATIONS))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    });

    it('unauthenticated fails to get user organizations', function () {
        $this->getJson(route(\App\Constants\Routes::GET_USER_ORGANIZATIONS))
            ->assertStatus(401);
    });

    it('fetch org details', function () {
        $this->actingAs($this->user);
        $organization = \App\Models\V1\Organization::factory()->create();

        $organization->members()->attach($this->user->id, [
            'role' => \App\Enums\OrganizationMembershipRoleEnum::MEMBER->value,
        ]);

        $this->getJson(route(\App\Constants\Routes::GET_ORGANIZATION_DETAILS, $organization->uuid))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'logoUrl',
                    'plan',
                    'isActive',
                    'createdAt',
                    'membersCount',
                    'updatedAt'
                ]
            ]);
    });

    it('fails to fetch org details if user do not belong to organization', function () {
        $newUser = \App\Models\V1\User::factory()->create();
        $this->actingAs($newUser);
        $organization = \App\Models\V1\Organization::factory()->create();

        $organization->members()->attach($this->user->id, [
            'role' => \App\Enums\OrganizationMembershipRoleEnum::MEMBER->value,
        ]);

        $this->getJson(route(\App\Constants\Routes::GET_ORGANIZATION_DETAILS, $organization->uuid))
            ->assertStatus(403);
    });

    it('fetch all members in org', function () {
        $this->actingAs($this->user);
        $organization = \App\Models\V1\Organization::factory()->create();

        $organization->members()->attach($this->user->id, [
            'role' => \App\Enums\OrganizationMembershipRoleEnum::MEMBER->value,
        ]);

        $this->getJson(route(\App\Constants\Routes::GET_ORGANIZATION_MEMBERS, $organization->uuid))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    });

    it('fails to fetch members in org if user is not a member', function () {
        $organization = \App\Models\V1\Organization::factory()->create();
        $this->actingAs($this->user);

        $this->getJson(route(\App\Constants\Routes::GET_ORGANIZATION_MEMBERS, $organization->uuid))
            ->assertStatus(403);
    });
});

describe('updates org', function () {
    it('updates org', function () {
        $this->actingAs($this->user);
        $organization = \App\Models\V1\Organization::factory()->create();

        $organization->members()->attach($this->user->id, [
            'role' => \App\Enums\OrganizationMembershipRoleEnum::OWNER->value,
        ]);

        $this->patch(route(\App\Constants\Routes::UPDATE_ORGANIZATION, $organization->uuid), [
            'name' => 'updated org name'
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'updated org name');
    });

    it('fails to update if user is not a owner or admin', function () {
        $this->actingAs($this->user);
        $organization = \App\Models\V1\Organization::factory()->create();

        $this->patch(route(\App\Constants\Routes::UPDATE_ORGANIZATION, $organization->uuid), [
            'name' => 'updated name'
        ])
            ->assertStatus(403);
    });
});

describe('delete organization', function () {
    it('owner delete organization', function () {
        $this->actingAs($this->user);
        $organization = \App\Models\V1\Organization::factory()->create();

        $organization->members()->attach($this->user->id, [
            'role' => \App\Enums\OrganizationMembershipRoleEnum::OWNER->value,
        ]);


        $this->deleteJson(route(\App\Constants\Routes::DESTROY_ORGANIZATION, $organization->uuid))
            ->assertStatus(204);

        $this->assertSoftDeleted('organizations', [
            'uuid' => $organization->uuid
        ]);
    });

    it('fails to delete organization if not owner', function () {
        $this->actingAs($this->user);
        $organization = \App\Models\V1\Organization::factory()->create();

        $organization->members()->attach($this->user->id, [
            'role' => \App\Enums\OrganizationMembershipRoleEnum::ADMIN->value,
        ]);


        $this->deleteJson(route(\App\Constants\Routes::DESTROY_ORGANIZATION, $organization->uuid))
            ->assertStatus(403);
    });
});
