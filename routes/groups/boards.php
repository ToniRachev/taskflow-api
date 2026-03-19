<?php

use App\Constants\Routes;
use App\Http\Controllers\V1\BoardController;

Route::controller(BoardController::class)->prefix('{board}')->group(function () {
    Route::get('/', 'show')->name(Routes::BOARD_SHOW);
    Route::patch('/', 'update')->name(Routes::BOARD_UPDATE);
    Route::delete('/', 'destroy')->name(Routes::BOARD_DESTROY);

    Route::prefix('/columns')->group(base_path('routes/groups/columns.php'));
});
