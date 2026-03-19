<?php

// Store

use App\Models\V1\Organization;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Models\V1\User::factory()->create();
    $this->organization = Organization::factory()->create();
    $this->board = \App\Models\V1\Board::factory()->create();
    $this->column = \App\Models\V1\Column::factory()->create();
    $this->payload = [
        'name' => fake()->sentence(),
        'color' => null,
        'order' => 0,
        'wip_limit' => 0
    ];
});

describe('Post /columns - store', function () {
    it('creates column', function () {
        $this->actingAs($this->user);
        $this->postJson(route(\App\Constants\Routes::COLUMN_STORE, [$this->board->uuid]), $this->payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'order',
                    'color',
                    'wipLimit',
                ]
            ]);
    });

    it('fails to create if not authenticated', function () {
        $this->postJson(route(\App\Constants\Routes::COLUMN_STORE, [$this->board->uuid]), $this->payload)
            ->assertStatus(401);
    });

    it('fails to create if not authorized', function () {
        $user = \App\Models\V1\User::factory()->create();
        $this->actingAs($user);
        $this->postJson(route(\App\Constants\Routes::COLUMN_STORE, [$this->board->uuid]), $this->payload)
            ->assertStatus(403);
    });

    it('fails to create if name missing', function () {
        $this->actingAs($this->user);
        unset($this->payload['name']);
        $this->postJson(route(\App\Constants\Routes::COLUMN_STORE, [$this->board->uuid]), $this->payload)
            ->assertStatus(422);
    });
});
