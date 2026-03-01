<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\RegisterUserRequest;
use App\Http\Requests\V1\LoginAuthRequest;
use App\Http\Resources\V1\UserResource;
use App\Responses\V1\ApiResponse;
use App\Services\V1\AuthService;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterUserRequest $request)
    {
        $result = $this->authService->register($request->validated());
        return ApiResponse::created(
            'User created successfully',
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ]
        );
    }

    public function login(LoginAuthRequest $request)
    {
        $result = $this->authService->login($request->validated());

        return ApiResponse::ok(
            'User logged in successfully',
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ]
        );
    }
}
