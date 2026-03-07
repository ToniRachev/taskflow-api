<?php

namespace App\Http\Controllers\V1;

use App\Constants\Message;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginAuthRequest;
use App\Http\Requests\V1\Auth\RegisterAuthRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Responses\V1\ApiResponse;
use App\Services\V1\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterAuthRequest $request)
    {
        $response = $this->authService->register($request->validated());
        return ApiResponse::created(
            Message::USER_REGISTERED,
            [
                'user' => UserResource::make($response['user']),
                'token' => $response['token'],
            ]
        );
    }

    public function login(LoginAuthRequest $request)
    {
        $result = $this->authService->login($request->validated());

        return ApiResponse::ok(
            Message::USER_LOGIN,
            [
                'user' => UserResource::make($result['user']),
                'token' => $result['token'],
            ]
        );
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return ApiResponse::noContent();
    }

    public function logoutAll(Request $request)
    {
        $this->authService->logoutAll($request->user());
        return ApiResponse::noContent();
    }

    public function refreshToken(Request $request)
    {
        $token = $this->authService->refreshToken($request->user());
        return ApiResponse::ok(data: $token);
    }
}
