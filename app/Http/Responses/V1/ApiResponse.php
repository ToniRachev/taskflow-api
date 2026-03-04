<?php

namespace App\Http\Responses\V1;

use App\Constants\Message;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    private static function success($message = null, $data = null, $code = 200): JsonResponse
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

    public static function ok($message = 'Ok', $data = null): JsonResponse
    {
        return self::success($message, $data);
    }

    public static function created($message = Message::RESOURCE_CREATED, $data = null): JsonResponse
    {
        return self::success($message, $data, 201);
    }

    public static function noContent(): JsonResponse
    {
        return self::success(code: 204);
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

    public static function invalidCredentials(): JsonResponse
    {
        return self::error(Message::INVALID_CREDENTIALS, null, 401);
    }

    public static function unauthorized(): JsonResponse
    {
        return self::error(Message::UNAUTHORIZED, null, 401);
    }
}
