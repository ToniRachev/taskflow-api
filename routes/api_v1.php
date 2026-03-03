<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post(Routes::REGISTER, [AuthController::class, 'register'])->name(Routes::REGISTER);
});
