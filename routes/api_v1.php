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
    Route::get(Routes::GET_PROFILE, [ProfileController::class, 'show'])->name(Routes::GET_PROFILE);
    Route::patch(Routes::PROFILE, [ProfileController::class, 'update'])->name(Routes::PROFILE);
    Route::patch(Routes::PROFILE . '/' . Routes::PREFERENCES, [ProfileController::class, 'updatePreferences']);
    ROUTE::post(Routes::PROFILE . '/' . Routes::AVATAR, [ProfileController::class, 'updateAvatar'])->name(Routes::AVATAR);
    ROUTE::delete(Routes::PROFILE . '/' . Routes::AVATAR, [ProfileController::class, 'deleteAvatar'])->name(Routes::AVATAR);
});
