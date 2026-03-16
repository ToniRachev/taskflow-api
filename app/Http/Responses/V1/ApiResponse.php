<?php

namespace App\Http\Responses\V1;

use App\Constants\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use function collect;

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

    public static function withPagination(
        LengthAwarePaginator $paginator,
                             $resource,
        string               $method = 'collection'
    ): JsonResponse
    {
        $collection = collect($paginator->items());
        $items = $resource::$method($collection);
        return self::ok(
            data: [
                'items' => $items,
                'pagination' => [
                    'meta' => [
                        'currentPage' => $paginator->currentPage(),
                        'lastPage' => $paginator->lastPage(),
                        'perPage' => $paginator->perPage(),
                        'total' => $paginator->total(),
                    ],
                    'links' => $paginator->linkCollection(),
                ]
            ]
        );
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

    public static function unauthenticated(): JsonResponse
    {
        return self::error(Message::UNAUTHENTICATED, null, 401);
    }

    public static function notFound($message = Message::RESOURCE_NOT_FOUND, $errors = null): JsonResponse
    {
        return self::error($message, $errors, 404);
    }

    public static function unauthorized($message = Message::UNAUTHORIZED, $errors = null): JsonResponse
    {
        return self::error($message, $errors, 403);
    }
}
