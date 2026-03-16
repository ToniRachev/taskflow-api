<?php


use App\Constants\Routes;
use App\Http\Controllers\V1\OrganizationController;
use App\Http\Controllers\V1\ProjectController;

Route::post('/', [OrganizationController::class, 'store'])->name(Routes::STORE_ORGANIZATION);
Route::get('/', [OrganizationController::class, 'index'])->name(Routes::GET_USER_ORGANIZATIONS);

Route::prefix('/{organization}')->group(function () {
    Route::get('/', [OrganizationController::class, 'show'])->name(Routes::GET_ORGANIZATION_DETAILS);
    Route::get('/members', [OrganizationController::class, 'members'])->name(Routes::GET_ORGANIZATION_MEMBERS);
    Route::patch('/', [OrganizationController::class, 'update'])->name(Routes::UPDATE_ORGANIZATION);
    Route::delete('/', [OrganizationController::class, 'destroy'])->name(Routes::DESTROY_ORGANIZATION);

    Route::prefix('/projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name(Routes::INDEX_PROJECT);
        Route::post('/', [ProjectController::class, 'store'])->name(Routes::STORE_PROJECT);
    });
});
