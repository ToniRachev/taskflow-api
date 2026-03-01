<?php

use App\Exceptions\V1\InvalidCredentialsException;
use App\Responses\V1\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->name('api.v1.')
                ->group(base_path('routes/api_v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::validationError(errors: $e->errors());
        });

        $exceptions->render(function (QueryException $e) {
            return ApiResponse::serverError();
        });

        $exceptions->render(function (InvalidCredentialsException $e) {
            return ApiResponse::invalidCredentials();
        });

        $exceptions->render(function (Throwable $e) {
            return ApiResponse::serverError();
        });
    })->create();
