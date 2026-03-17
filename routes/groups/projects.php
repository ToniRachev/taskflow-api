<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\ProjectController;
use App\Http\Controllers\V1\TaskController;


Route::prefix('/{project}')->group(function () {
    Route::get('/', [ProjectController::class, 'show'])->name(Routes::PROJECT_SHOW);
    Route::patch('/', [ProjectController::class, 'update'])->name(Routes::PROJECT_UPDATE);
    Route::delete('/', [ProjectController::class, 'destroy'])->name(Routes::PROJECT_DESTROY);
    Route::post('/archive', [ProjectController::class, 'archiveProject'])->name(Routes::PROJECT_ARCHIVE);

    Route::prefix('/boards')->group(base_path('routes/groups/boards.php'));

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name(Routes::TASK_INDEX);
        Route::post('/', [TaskController::class, 'store'])->name(Routes::TASK_STORE);
        Route::patch('/bulk-status', [TaskController::class, 'updateBulkStatus'])->name(Routes::TASK_STATUS_BULK);
    });
});
