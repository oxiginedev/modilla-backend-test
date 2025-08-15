<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case FREELANCER = 'freelancer';
    case CLIENT = 'client';
    case ADMIN = 'admin';

    public static function toArray(): array
    {
        return [
            self::ADMIN,
            self::CLIENT,
            self::FREELANCER,
        ];
    }
}
