<?php

use App\Exceptions\InvalidCredentialsException;
use App\Http\Middleware\UpdateLastSeenMiddleware;
use App\Http\Responses\V1\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix(config('api.prefix'))
                ->group(base_path('routes/api_' . config('api.version') . '.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', [
            UpdateLastSeenMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::validationError(errors: $e->errors());
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::unauthorized();
        });

        $exceptions->render(function (InvalidCredentialsException $e) {
            return ApiResponse::invalidCredentials();
        });

        $exceptions->render(function (QueryException $e) {
            Log::error($e->getMessage());
            return ApiResponse::serverError();
        });

        $exceptions->render(function (Throwable $e) {
            Log::error($e->getMessage());
            return ApiResponse::serverError();
        });
    })->create();
