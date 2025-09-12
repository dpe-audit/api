<?php

namespace App\Domain\Ecs\Systeme\Reseau;

enum IsolationReseau: string
{
    case ISOLE = 'isole';
    case NON_ISOLE = 'non_isole';

    public static function try_from_reseau_distribution_isole(null|bool|int $value): ?self
    {
        return match ($value) {
            1, true => self::ISOLE,
            0, false => self::NON_ISOLE,
            default => null,
        };
    }
}
