<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\OrganizationController;
use App\Http\Controllers\V1\ProfileController;
use App\Http\Controllers\V1\ProjectController;
use App\Http\Controllers\V1\TaskController;

//TODO Extract paths

Route::prefix('auth')->group(base_path('routes/groups/auth.php'));

Route::prefix('profile')->middleware('auth:sanctum')->group(base_path('routes/groups/profile.php'));
Route::prefix('organizations')->middleware('auth:sanctum')->group(base_path('routes/groups/organizations.php'));

//Route::prefix('organizations')->middleware('auth:sanctum')->group(function () {
//    Route::post('/', [OrganizationController::class, 'store'])->name(Routes::STORE_ORGANIZATION);
//    Route::get('/', [OrganizationController::class, 'index'])->name(Routes::GET_USER_ORGANIZATIONS);
//
//    Route::prefix('/{organization}')->group(function () {
//        Route::get('/', [OrganizationController::class, 'show'])->name(Routes::GET_ORGANIZATION_DETAILS);
//        Route::get('/members', [OrganizationController::class, 'members'])->name(Routes::GET_ORGANIZATION_MEMBERS);
//        Route::patch('/', [OrganizationController::class, 'update'])->name(Routes::UPDATE_ORGANIZATION);
//        Route::delete('/', [OrganizationController::class, 'destroy'])->name(Routes::DESTROY_ORGANIZATION);
//
//        Route::prefix('projects')->group(function () {
//            Route::get('/', [ProjectController::class, 'index'])->name(Routes::INDEX_PROJECT);
//            Route::post('/', [ProjectController::class, 'store'])->name(Routes::STORE_PROJECT);
//            Route::prefix('/{project}')->group(function () {
//                Route::prefix('tasks')->group(function () {
//                    Route::get('/', [TaskController::class, 'index'])->name(Routes::INDEX_TASK);
//                    Route::post('/', [TaskController::class, 'store'])->name(Routes::STORE_TASK);
//                    Route::patch('/bulk-status', [TaskController::class, 'updateBulkStatus'])->name(Routes::BULK_UPDATE_TASK_STATUS);
//
//                    Route::prefix('/{task}')->group(function () {
//                        Route::get('/', [TaskController::class, 'show'])->name(Routes::SHOW_TASK);
//                        Route::patch('/', [TaskController::class, 'update'])->name(Routes::UPDATE_TASK);
//                        Route::delete('/', [TaskController::class, 'destroy'])->name(Routes::DESTROY_TASK);
//                        Route::patch('/status', [TaskController::class, 'updateStatus'])->name(Routes::UPDATE_TASK_STATUS);
//                        Route::patch('/assign', [TaskController::class, 'updateAssignee'])->name(Routes::UPDATE_TASK_ASSIGNEE);
//                        Route::patch('/priority', [TaskController::class, 'updatePriority'])->name(Routes::UPDATE_TASK_PRIORITY);
//                        Route::get('/subtasks', [TaskController::class, 'indexSubtask'])->name(Routes::INDEX_SUBTASK);
//                        Route::post('/subtasks', [TaskController::class, 'storeSubtask'])->name(Routes::STORE_SUBTASK);
//                        Route::get('/activity', [TaskController::class, 'indexActivity'])->name(Routes::INDEX_TASK_ACTIVITY);
//                    });
//                });
//
//                Route::get('/', [ProjectController::class, 'show'])->name(Routes::SHOW_PROJECT);
//                Route::patch('/', [ProjectController::class, 'update'])->name(Routes::UPDATE_PROJECT);
//                Route::delete('/', [ProjectController::class, 'destroy'])->name(Routes::DESTROY_PROJECT);
//                Route::post('/archive', [ProjectController::class, 'archiveProject'])->name(Routes::ARCHIVE_PROJECT);
//            });
//        });
//    });
//});
