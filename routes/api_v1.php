<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;

Route::prefix(Routes::AUTH_MODULE)->name(Routes::AUTH_MODULE . '.')->group(function () {
    Route::post(Routes::REGISTER, [AuthController::class, 'register'])->name(Routes::REGISTER);
    Route::post(Routes::LOGIN, [AuthController::class, 'login'])->name(Routes::LOGIN);
});
