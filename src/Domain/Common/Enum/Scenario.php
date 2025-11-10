<?php

namespace App\Domain\Common\Enum;

enum Scenario: string
{
    case CONVENTIONNEL = 'conventionnel';
    case DEPENSIER = 'depensier';

    public static function each(\Closure $func): array
    {
        return \array_map($func, self::cases());
    }
}
