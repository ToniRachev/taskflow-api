<?php

namespace App\Http\Responses\V1;

use App\Constants\Message;
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


    //---Success-----------------------

    public static function created($message = Message::RESOURCE_CREATED, $data = null): JsonResponse
    {
        return self::success($message, $data, 201);
    }

    //---Errors-----------------------

    public static function validationError($message = Message::VALIDATION_ERROR, $errors = null): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    public static function serverError($message = Message::SERVER_ERROR, $errors = null): JsonResponse
    {
        return self::error($message, $errors, 500);
    }
}
