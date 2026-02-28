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

    public function ok($message, $data = null): JsonResponse
    {
        return self::success($message, $data);
    }

    public function created($message = 'Resource created successfully', $data = null): JsonResponse
    {
        return self::success($message, $data, 201);
    }
}
