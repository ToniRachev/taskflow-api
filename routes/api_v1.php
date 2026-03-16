<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\OrganizationController;
use App\Http\Controllers\V1\ProfileController;
use App\Http\Controllers\V1\ProjectController;
use App\Http\Controllers\V1\TaskController;

//TODO Extract paths

Route::prefix('auth')->group(base_path('routes/groups/auth.php'));

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('profile')->group(base_path('routes/groups/profile.php'));
    Route::prefix('organizations')->group(base_path('routes/groups/organizations.php'));
    Route::prefix('projects')->group(base_path('routes/groups/projects.php'));
    Route::prefix('tasks')->group(base_path('routes/groups/tasks.php'));
});
