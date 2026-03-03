<?php

use App\Constants\Routes;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


describe('Register', function () {
    beforeEach(function () {
        $this->payload = [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'X9#mK2$pQwLz!nR4',
        ];

        $this->registerRoute = route(Routes::API_VERSION . '.' . Routes::AUTH_MODULE . '.' . Routes::REGISTER);
    });

    it('should register a user and returns the correct structure', function () {
        $response = $this->postJson($this->registerRoute, $this->payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user', 'token']
            ]);

        $this->assertDatabaseHas('users', ['email' => $this->payload['email']]);
        expect($response->json('data.token'))->toBeString()->not->toBeEmpty();
    });

    it('fail if the email is already registered', function () {
        \App\Models\User::factory()->create([
            'email' => 'test@gmail.com'
        ]);

        $this->postJson($this->registerRoute, $this->payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('fails validation on missing data', function ($invalidData, $field) {
        $this->postJson($this->registerRoute, array_merge($this->payload, $invalidData))
            ->assertStatus(422)
            ->assertJsonValidationErrors([$field]);
    })->with([
        'missing name' => [['name' => ''], 'name'],
        'missing email' => [['email' => ''], 'email'],
        'missing password' => [['password' => ''], 'password'],
    ]);

    it('fail validation on incorrect data', function ($invalidData, $field) {
        $this->postJson($this->registerRoute, array_merge($this->payload, $invalidData))
            ->assertStatus(422)
            ->assertJsonValidationErrors([$field]);
    })->with([
        'invalid email' => [['email' => 'test'], 'email'],
        'invalid password' => [['password' => 'test'], 'password'],
        'long name' => [['name' => str_repeat('a', 256)], 'name'],
    ]);

    it('hash password', function () {
        $response = $this->postJson($this->registerRoute, $this->payload);

        $user = \App\Models\User::where('email', $this->payload['email'])->first();
        $this->assertNotEquals($this->payload['password'], $user->password);
    });
});
