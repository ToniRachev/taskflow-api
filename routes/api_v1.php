<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\OrganizationController;
use App\Http\Controllers\V1\ProfileController;
use App\Http\Controllers\V1\ProjectController;

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
    Route::patch('preferences', [ProfileController::class, 'updatePreferences'])->name(Routes::UPDATE_PREFERENCES);
    ROUTE::post('avatar', [ProfileController::class, 'updateAvatar'])->name(Routes::STORE_AVATAR);
    ROUTE::delete('avatar', [ProfileController::class, 'deleteAvatar'])->name(Routes::DESTROY_AVATAR);
});

Route::prefix('organizations')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [OrganizationController::class, 'store'])->name(Routes::STORE_ORGANIZATION);
    Route::get('/', [OrganizationController::class, 'index'])->name(Routes::GET_USER_ORGANIZATIONS);

    Route::prefix('/{organization}')->group(function () {
        Route::get('/', [OrganizationController::class, 'show'])->name(Routes::GET_ORGANIZATION_DETAILS);
        Route::get('/members', [OrganizationController::class, 'members'])->name(Routes::GET_ORGANIZATION_MEMBERS);
        Route::patch('/', [OrganizationController::class, 'update'])->name(Routes::UPDATE_ORGANIZATION);
        Route::delete('/', [OrganizationController::class, 'destroy'])->name(Routes::DESTROY_ORGANIZATION);

        Route::prefix('projects')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name(Routes::INDEX_PROJECT);
            Route::post('/', [ProjectController::class, 'store'])->name(Routes::STORE_PROJECT);
            Route::prefix('/{project}')->group(function () {
                Route::get('/', [ProjectController::class, 'show'])->name(Routes::SHOW_PROJECT);
                Route::patch('/', [ProjectController::class, 'update'])->name(Routes::UPDATE_PROJECT);
                Route::delete('/', [ProjectController::class, 'destroy'])->name(Routes::DESTROY_PROJECT);
                Route::post('/archive', [ProjectController::class, 'archiveProject'])->name(Routes::ARCHIVE_PROJECT);
            });
        });
    });
});
