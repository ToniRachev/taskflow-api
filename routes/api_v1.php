<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\ProfileController;

Route::prefix(Routes::AUTH_MODULE)->name(Routes::AUTH_MODULE . '.')->group(function () {
    Route::post(Routes::REGISTER, [AuthController::class, 'register'])->name(Routes::REGISTER);
    Route::post(Routes::LOGIN, [AuthController::class, 'login'])->name(Routes::LOGIN);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post(Routes::LOGOUT, [AuthController::class, 'logout'])->name(Routes::LOGOUT);
        Route::post(Routes::LOGOUT_ALL, [AuthController::class, 'logoutAll'])->name(Routes::LOGOUT_ALL);
        Route::post(Routes::REFRESH_TOKEN, [AuthController::class, 'refreshToken'])->name(Routes::REFRESH_TOKEN);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [ProfileController::class, 'show']);
    Route::patch('profile', [ProfileController::class, 'update']);
    Route::patch('profile/preferences', [ProfileController::class, 'updatePreferences']);
});
