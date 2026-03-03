<?php

namespace App\Http\Responses\V1;

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

    private static function error($message, $errors, $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
            'message' => $message,
        ], $code);
    }

    public static function created($message = 'Resource created successfully', $data = null): JsonResponse
    {
        return self::success($message, $data, 201);
    }
}
