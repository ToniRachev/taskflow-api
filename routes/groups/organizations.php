<?php


use App\Constants\Routes;
use App\Http\Controllers\V1\OrganizationController;
use App\Http\Controllers\V1\ProjectController;

Route::post('/', [OrganizationController::class, 'store'])->name(Routes::ORGANIZATION_STORE);
Route::get('/', [OrganizationController::class, 'index'])->name(Routes::ORGANIZATION_INDEX);

Route::prefix('/{organization}')->group(function () {
    Route::get('/', [OrganizationController::class, 'show'])->name(Routes::ORGANIZATION_SHOW);
    Route::get('/members', [OrganizationController::class, 'members'])->name(Routes::ORGANIZATION_MEMBERS_INDEX);
    Route::patch('/', [OrganizationController::class, 'update'])->name(Routes::ORGANIZATION_UPDATE);
    Route::delete('/', [OrganizationController::class, 'destroy'])->name(Routes::ORGANIZATION_DESTROY);

    Route::prefix('/projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name(Routes::PROJECT_INDEX);
        Route::post('/', [ProjectController::class, 'store'])->name(Routes::PROJECT_STORE);
    });
});
