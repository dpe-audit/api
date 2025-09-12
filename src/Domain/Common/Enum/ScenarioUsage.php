<?php

namespace App\Domain\Common\Enum;

enum ScenarioUsage: string
{
    case CONVENTIONNEL = 'conventionnel';
    case DEPENSIER = 'depensier';

    public static function each(\Closure $func): array
    {
        return \array_map($func, self::cases());
    }
}
