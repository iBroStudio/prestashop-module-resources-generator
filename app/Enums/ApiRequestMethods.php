<?php

namespace App\Enums;

enum ApiRequestMethods: string
{
    case GET = 'get';

    public static function getSelector(): array
    {
        return array_reduce(self::cases(), function ($list, $enum) {
            $list[] = $enum->value;

            return $list;
        }, []);
    }
}
