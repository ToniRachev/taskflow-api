<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\ProjectController;
use App\Http\Controllers\V1\TaskController;


Route::prefix('/{project}')->group(function () {
    Route::get('/', [ProjectController::class, 'show'])->name(Routes::SHOW_PROJECT);
    Route::patch('/', [ProjectController::class, 'update'])->name(Routes::UPDATE_PROJECT);
    Route::delete('/', [ProjectController::class, 'destroy'])->name(Routes::DESTROY_PROJECT);
    Route::post('/archive', [ProjectController::class, 'archiveProject'])->name(Routes::ARCHIVE_PROJECT);

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name(Routes::INDEX_TASK);
        Route::post('/', [TaskController::class, 'store'])->name(Routes::STORE_TASK);
        Route::patch('/bulk-status', [TaskController::class, 'updateBulkStatus'])->name(Routes::BULK_UPDATE_TASK_STATUS);
    });
});
