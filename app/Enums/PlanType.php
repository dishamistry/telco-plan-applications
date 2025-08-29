<?php

namespace App\Enums;

enum PlanType: string
{
    case NBN = 'nbn';
    case OPTICOMM = 'opticomm';
    case MOBILE = 'mobile';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
