<?php

namespace App\Services\V1;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

final class AuthService
{
    private function invokeToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    private function revokeToken(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    public function register($userData): array
    {
        return DB::transaction(function () use ($userData) {
            $user = User::create($userData);
            $token = $this->invokeToken($user);
            return compact('user', 'token');
        });
    }

    public function login($userData): array
    {
        $user = User::where('email', $userData['email'])->first();
        if (!$user || !Hash::check($userData['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $this->invokeToken($user);
        return compact('user', 'token');
    }
}
