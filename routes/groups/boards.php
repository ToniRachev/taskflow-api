<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\BoardController;

Route::controller(BoardController::class)->group(function () {
    Route::get('/{board}', 'show')->name(Routes::BOARD_SHOW);
    Route::patch('/{board}', 'update')->name(Routes::BOARD_UPDATE);
});
