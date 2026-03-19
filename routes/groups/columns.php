<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\ColumnController;
use Illuminate\Support\Facades\Route;

Route::controller(ColumnController::class)->group(function () {
    Route::post('/', 'store')->name(Routes::COLUMN_STORE);
});
