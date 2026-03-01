<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\RegisterUserRequest;
use App\Http\Requests\V1\LoginAuthRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Responses\V1\ApiResponse;
use App\Services\V1\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
