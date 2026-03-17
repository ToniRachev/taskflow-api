<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('profile', function () {
    beforeEach(function () {
        $this->user = \App\Models\V1\User::factory()->create();
        $this->token = $this->user->createToken('auth_token')->plainTextToken;
    });

    it('creates a profile on registration', function () {
        $this->postJson(route(\App\Constants\Routes::REGISTER), [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'X9#mK2$pQwLz!nR4',
        ]);

        $user = \App\Models\V1\User::where('email', 'test@gmail.com')->first();

        expect($user->profile)->not->toBeNull()
            ->and($user->profile instanceof \App\Models\V1\Profile)->toBeTrue();
    });

    it('update profile', function ($field, $value, $dbField = null) {
        $this->withToken($this->token)->patchJson(route(\App\Constants\Routes::PROFILE_UPDATE), [
            $field => $value
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.profile.' . $field, $value);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $this->user->id,
                $dbField ?? $field => $value
        ]);
    })
        ->with([
            'bio' => ['bio', 'test bio'],
            'phone' => ['phone', '+555251255'],
            'githubUrl' => ['githubUrl', 'https://github.com/test', 'github_url'],
        ]);

    it('fails to update profile with invalid data', function ($field, $value, $dbField = null) {
        $this->withToken($this->token)->patchJson(route(\App\Constants\Routes::PROFILE_UPDATE), [
            $field => $value
        ])
            ->assertStatus(422);
    })
        ->with([
            'bio' => ['bio', str_repeat('a', 501)],
            'phone' => ['phone', 'invalid phone'],
            'githubUrl' => ['githubUrl', 'invalid url'],
        ]);

    it('updates preferences', function ($field, $value) {
        $this->withToken($this->token)->patchJson(route(\App\Constants\Routes::PROFILE_PREFERENCES_UPDATE), [
            $field => $value
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.profile.preferences.' . $field, $value);

        $profile = \App\Models\V1\Profile::where('user_id', $this->user->id)->first();
        $this->assertEquals($value, $profile->preferences[$field]);
    })
        ->with([
            'theme' => ['theme', 'light'],
        ]);

    it('fails for invalid preferences', function ($field, $value) {
        $this->withToken($this->token)->patchJson(route(\App\Constants\Routes::PROFILE_PREFERENCES_UPDATE), [
            $field => $value
        ])
            ->assertStatus(422);

    })
        ->with([
            'theme' => ['theme', 'blue'],
        ]);

    it('updates notifications', function ($field, $value) {
        $this->withToken($this->token)->patchJson(route(\App\Constants\Routes::PROFILE_PREFERENCES_UPDATE), [
            'notifications' => [
                $field => $value
            ]
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.profile.preferences.notifications.' . $field, $value);
    })
        ->with([
            'taskAssigned' => ['taskAssigned', true],
            'email' => ['email', false],
            'mentioned' => ['mentioned', true],
        ]);

    it('fails on invalid notification data', function ($field, $value) {
        $this->withToken($this->token)->patchJson(route(\App\Constants\Routes::PROFILE_PREFERENCES_UPDATE), [
            'notifications' => [
                $field => $value
            ]
        ])
            ->assertStatus(422);
    })
        ->with([
            'taskAssigned' => ['taskAssigned', 'test'],
            'email' => ['email', 'false'],
            'mentioned' => ['mentioned', 'hello'],
        ]);

    it('upload avatar', function () {
        Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg');

        $this->withToken($this->token)->post(route(\App\Constants\Routes::PROFILE_AVATAR_STORE), [
            'avatar' => $file
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.profile.avatarUrl', fn($url) => str_contains($url, $file->hashName()));

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    });

    it('delete avatar', function () {
        Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg')->store('avatars', 'public');
        $this->user->profile->forceFill(['avatar_url' => $file])->save();
        $this->withToken($this->token)->deleteJson(route(\App\Constants\Routes::PROFILE_AVATAR_STORE))
            ->assertStatus(204);
        Storage::disk('public')->assertMissing($file);
        $this->assertDatabaseHas('profiles', ['user_id' => $this->user->id, 'avatar_url' => null]);
    });

    it('fails to upload avatar when no file provided', function () {
        $this->withToken($this->token)->postJson(route(\App\Constants\Routes::PROFILE_AVATAR_STORE))
            ->assertStatus(422);
    });

    it('fails to upload avatar when invalid file provided', function () {
        Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('invalid.txt');
        $this->withToken($this->token)->postJson(route(\App\Constants\Routes::PROFILE_AVATAR_STORE), [
            'avatar' => $file
        ])
            ->assertStatus(422);
    });
});
