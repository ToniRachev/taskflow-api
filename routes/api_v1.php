<?php

use App\Http\Controllers\V1\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
});
