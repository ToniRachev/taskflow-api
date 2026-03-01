<?php

namespace App\Services\V1;

use App\Exceptions\V1\InvalidCredentialsException;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Responses\V1\ApiResponse;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    private function issueToken($user): string
    {
        $expiresAt = now()->addDays(30);
        return $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;
    }

    public function register($userData): array
    {
        $user = User::create([
            ...$userData,
            'password' => Hash::make($userData['password']),
        ]);

        $token = $this->issueToken($user);
        return compact('user', 'token');
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
        $user->currentAccessToken()->delete();
    }

    public function logoutAll($user): void
    {
        $user->tokens()->delete();
    }
}
