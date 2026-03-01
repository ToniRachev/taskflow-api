<?php

namespace App\Services\V1;

use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Responses\V1\ApiResponse;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    // Token handlers
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
}
