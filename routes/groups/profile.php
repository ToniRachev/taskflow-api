<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\ProfileController;

Route::get('/', [ProfileController::class, 'show'])->name(Routes::GET_PROFILE);
Route::patch('/', [ProfileController::class, 'update'])->name(Routes::PROFILE_UPDATE);
Route::patch('preferences', [ProfileController::class, 'updatePreferences'])->name(Routes::PROFILE_PREFERENCES_UPDATE);
ROUTE::post('avatar', [ProfileController::class, 'updateAvatar'])->name(Routes::PROFILE_AVATAR_STORE);
ROUTE::delete('avatar', [ProfileController::class, 'deleteAvatar'])->name(Routes::PROFILE_AVATAR_DESTROY);
