<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\ProfileController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name(Routes::REGISTER);
    Route::post('login', [AuthController::class, 'login'])->name(Routes::LOGIN);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name(Routes::LOGOUT);
        Route::post('logout-all', [AuthController::class, 'logoutAll'])->name(Routes::LOGOUT_ALL);
        Route::post('refresh-token', [AuthController::class, 'refreshToken'])->name(Routes::REFRESH_TOKEN);
    });
});

Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name(Routes::GET_PROFILE);
    Route::patch('/', [ProfileController::class, 'update'])->name(Routes::UPDATE_PROFILE);
    Route::patch('preferences', [ProfileController::class, 'updatePreferences'])->name(Routes::PREFERENCES);
    ROUTE::post('avatar', [ProfileController::class, 'updateAvatar'])->name(Routes::STORE_AVATAR);
    ROUTE::delete('avatar', [ProfileController::class, 'deleteAvatar'])->name(Routes::DESTROY_AVATAR);
});
