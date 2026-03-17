<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\TaskController;

Route::prefix('/{task}')->group(function () {
    Route::get('/', [TaskController::class, 'show'])->name(Routes::TASK_SHOW);
    Route::patch('/', [TaskController::class, 'update'])->name(Routes::TASK_UPDATE);
    Route::delete('/', [TaskController::class, 'destroy'])->name(Routes::TASK_DESTROY);
    Route::patch('/status', [TaskController::class, 'updateStatus'])->name(Routes::TASK_STATUS_UPDATE);
    Route::patch('/assign', [TaskController::class, 'updateAssignee'])->name(Routes::TASK_ASSIGNEE_UPDATE);
    Route::patch('/priority', [TaskController::class, 'updatePriority'])->name(Routes::TASK_PRIORITY_UPDATE);
    Route::get('/subtasks', [TaskController::class, 'indexSubtask'])->name(Routes::TASK_SUBTASKS_INDEX);
    Route::post('/subtasks', [TaskController::class, 'storeSubtask'])->name(Routes::TASK_SUBTASKS_STORE);
    Route::get('/activity', [TaskController::class, 'indexActivity'])->name(Routes::TASK_ACTIVITY_INDEX);
});
