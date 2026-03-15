<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\ProjectController;
use App\Http\Controllers\V1\TaskController;

Route::get('/', [ProjectController::class, 'index'])->name(Routes::INDEX_PROJECT);
Route::post('/', [ProjectController::class, 'store'])->name(Routes::STORE_PROJECT);
Route::prefix('/{project}')->group(function () {
    Route::prefix('tasks')->group(base_path('routes/groups/tasks.php'));

    Route::get('/', [ProjectController::class, 'show'])->name(Routes::SHOW_PROJECT);
    Route::patch('/', [ProjectController::class, 'update'])->name(Routes::UPDATE_PROJECT);
    Route::delete('/', [ProjectController::class, 'destroy'])->name(Routes::DESTROY_PROJECT);
    Route::post('/archive', [ProjectController::class, 'archiveProject'])->name(Routes::ARCHIVE_PROJECT);
});
