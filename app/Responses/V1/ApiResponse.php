<?php

namespace App\Responses\V1;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    private static function success($message, $data, $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    private static function error($message, $errors, $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    // Success

    public static function ok($message, $data = null): JsonResponse
    {
        return self::success($message, $data);
    }

    public static function created($message = 'Resource created successfully', $data = null): JsonResponse
    {
        return self::success($message, $data, 201);
    }

    // Errors

    public static function validationError($message = 'Validation error', $errors = null): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    public static function serverError($message = 'Something went wrong. Please try again.', $errors = null): JsonResponse
    {
        return self::error($message, $errors);
    }

    public static function invalidCredentials(): JsonResponse
    {
        return self::error('Invalid email address or password.', null, 401);
    }
}
