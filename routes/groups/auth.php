<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;

Route::post('register', [AuthController::class, 'register'])->name(Routes::REGISTER);
Route::post('login', [AuthController::class, 'login'])->name(Routes::LOGIN);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name(Routes::LOGOUT);
    Route::post('logout-all', [AuthController::class, 'logoutAll'])->name(Routes::LOGOUT_ALL);
    Route::post('refresh-token', [AuthController::class, 'refreshToken'])->name(Routes::REFRESH_TOKEN);
});
