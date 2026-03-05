<?php

use App\Constants\Routes;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Login', function () {
    beforeEach(function () {
        $this->payload = [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'X9#mK2$pQwLz!nR4',
        ];

        $this->user = \App\Models\V1\User::factory()->create($this->payload);
        $this->loginRoute = route(Routes::LOGIN);
    });

    it('should login successfully and return the correct structure', function () {
        $response = $this->postJson($this->loginRoute, $this->payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user', 'token']
            ]);

        expect($response->json('data.token'))->toBeString()->not->toBeEmpty();
    });

    it('should fail for invalid credentials', function ($invalidData) {
        $this->postJson(
            $this->loginRoute, array_merge($this->payload, $invalidData))
            ->assertStatus(401);
    })
        ->with([
            'invalid email' => [
                ['email' => 'veryWrongEmail1234@gmail.com']
            ],
            'invalid password' => [
                ['password' => 'veryWrongPassword123$']
            ],
        ]);

    it('should fail for missing fields', function ($invalidData, $field) {
        $this->postJson(
            $this->loginRoute, array_merge($this->payload, $invalidData))
            ->assertStatus(422)
            ->assertJsonValidationErrors([$field]);
    })
        ->with([
            'invalid email' => [['email' => null], 'email'],
            'invalid password' => [['password' => null], 'password'],
        ]);
});
