<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\BoardController;

Route::controller(BoardController::class)->group(function () {
    Route::get('/', 'index')->name(Routes::BOARD_INDEX);
    Route::post('/', 'store')->name(Routes::BOARD_STORE);
});
