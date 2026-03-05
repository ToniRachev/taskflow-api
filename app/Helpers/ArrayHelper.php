<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ArrayHelper
{
    public static function toSnakeKeys($array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[Str::snake($key)] = is_array($value)
                ? self::toSnakeKeys($value)
                : $value;
        }

        return $result;
    }
}
