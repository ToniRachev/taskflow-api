<?php

use App\Constants\Routes;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->token = $this->user->createToken('auth_token')->plainTextToken;
    $this->logoutRoute = route(Routes::API_VERSION . '.' . Routes::AUTH_MODULE . '.' . Routes::LOGOUT);
    $this->logoutAllRoute = route(Routes::API_VERSION . '.' . Routes::AUTH_MODULE . '.' . Routes::LOGOUT_ALL);
    $this->refreshTokenRoute = route(Routes::API_VERSION . '.' . Routes::AUTH_MODULE . '.' . Routes::REFRESH_TOKEN);
});

describe('logout', function () {
    it('logout successfully', function () {

        $this->withToken($this->token)->postJson($this->logoutRoute)
            ->assertStatus(204);

        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $this->user->id]);
    });

    it('fail if the user is not authenticated', function () {
        $this->postJson($this->logoutRoute)
            ->assertStatus(401);
    });
});

describe('logoutAll', function () {
    it('logout all devices', function () {
        $this->user->createToken('mobile_token')->plainTextToken;
        $this->user->createToken('ipad_token')->plainTextToken;

        $this->withToken($this->token)->postJson($this->logoutAllRoute)
            ->assertStatus(204);

        expect($this->user->tokens)->toHaveCount(0);
    });

    it('fail if the user is not authenticated', function () {
        $this->postJson($this->logoutAllRoute)
            ->assertStatus(401);
    });
});

describe('refreshToken', function () {
    it('refresh token successfully', function () {
        $response = $this->withToken($this->token)->postJson($this->refreshTokenRoute)
            ->assertStatus(200);

        $newToken = $response->json('data');
        expect($newToken)
            ->toBeString()
            ->not->toBeEmpty()
            ->not->toBe($this->token);
    });

    it('return valid new token', function () {
        $response = $this->withToken($this->token)->postJson($this->refreshTokenRoute);
        $newToken = $response->json('data');

        $this->app['auth']->forgetGuards();

        $this->withToken($newToken)->postJson($this->logoutRoute)
            ->assertStatus(204);
    });

    it('invalidates old token', function () {
        $this->withToken($this->token)->postJson($this->refreshTokenRoute);
        $this->app['auth']->forgetGuards();
        $this->withToken($this->token)->postJson($this->refreshTokenRoute)
            ->assertStatus(401);
    });

    it('fails if the user is not authenticated', function () {
        $this->postJson($this->refreshTokenRoute)
            ->assertStatus(401);
    });
});
