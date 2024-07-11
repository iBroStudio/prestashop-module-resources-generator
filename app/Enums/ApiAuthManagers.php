<?php

namespace App\Enums;

enum ApiAuthManagers: string
{
    case BASIC = 'BasicAuthManager';
    case BEARER = 'BearerAuthManager';
    case HEADER = 'HeaderAuthManager';
    case NONE = 'null';

    public function label(): string
    {
        return match ($this) {
            self::BASIC => 'Bearer Authentication',
            self::BEARER => 'Basic Authentication',
            self::HEADER => 'Header Authentication',
            self::NONE => 'No Authentication',
        };
    }

    public static function getSelector(): array
    {
        return array_reduce(self::cases(), function ($list, $enum) {
            $list[$enum->label()] = $enum->value;

            return $list;
        }, []);
    }
}
