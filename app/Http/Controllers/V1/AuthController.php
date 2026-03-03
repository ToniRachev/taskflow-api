<?php

namespace App\Http\Controllers\V1;

use App\Constants\Message;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\RegisterAuthRequest;
use App\Http\Responses\V1\ApiResponse;
use App\Services\V1\AuthService;

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
                'user' => $response['user'],
                'token' => $response['token'],
            ]
        );
    }
}
