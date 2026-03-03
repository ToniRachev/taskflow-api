<?php

namespace App\Services\V1;

use App\Models\User;
use Illuminate\Support\Facades\DB;
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
}
