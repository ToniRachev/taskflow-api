<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\ProfileController;

Route::get('/', [ProfileController::class, 'show'])->name(Routes::GET_PROFILE);
Route::patch('/', [ProfileController::class, 'update'])->name(Routes::UPDATE_PROFILE);
Route::patch('preferences', [ProfileController::class, 'updatePreferences'])->name(Routes::UPDATE_PREFERENCES);
ROUTE::post('avatar', [ProfileController::class, 'updateAvatar'])->name(Routes::STORE_AVATAR);
ROUTE::delete('avatar', [ProfileController::class, 'deleteAvatar'])->name(Routes::DESTROY_AVATAR);
