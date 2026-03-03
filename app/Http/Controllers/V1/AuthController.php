<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\RegisterAuthRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\User;
use App\Constants\Message;

class AuthController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        return ApiResponse::created(
            Message::USER_REGISTERED,
            new UserResource(
                User::create($request->validated())
            )
        );
    }
}
