<?php

namespace App\Enums;

enum InternshipStatus: string
{
    case SUBMITTED = 'SUBMITTED';

    case CONFIRMED_BY_COMPANY = 'CONFIRMED_BY_COMPANY';
    case CONFIRMED_BY_ADMIN = 'CONFIRMED_BY_ADMIN';

    case DENIED_BY_COMPANY = 'DENIED_BY_COMPANY';
    case DENIED_BY_ADMIN = 'DENIED_BY_ADMIN';

    case DEFENDED = 'DEFENDED';
    case NOT_DEFENDED = 'NOT_DEFENDED';

    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
