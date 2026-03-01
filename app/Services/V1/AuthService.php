<?php

namespace App\Services\V1;

use App\Exceptions\V1\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    private function issueToken($user): string
    {
        $expiresAt = now()->addDays(30);
        return $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;
    }

    private function revokeToken($user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function refreshToken($user): string
    {
        $this->revokeToken($user);
        return $this->issueToken($user);
    }

    public function register($userData): array
    {
        return DB::transaction(function () use ($userData) {

            $user = User::create([
                ...$userData,
                'password' => Hash::make($userData['password']),
            ]);

            $token = $this->issueToken($user);
            return compact('user', 'token');
        });
    }

    public function login($userData): array
    {
        $user = User::where('email', $userData['email'])->first();

        if (!$user || !Hash::check($userData['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $this->issueToken($user);
        return compact('user', 'token');
    }

    public function logout($user): void
    {
        $this->revokeToken($user);
    }

    public function logoutAll($user): void
    {
        $user->tokens()->delete();
    }
}
