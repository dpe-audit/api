<?php

namespace App\Domain\Enveloppe\Baie\Survitrage;

enum TypeSurvitrage: string
{
    case SURVITRAGE_SIMPLE = 'survitrage_simple';
    case SURVITRAGE_FE = 'survitrage_fe';

    public static function try_from_opendata(int $id, ?bool $vitrage_vir): ?self
    {
        return match ($id) {
            4 => $vitrage_vir ? self::SURVITRAGE_FE : self::SURVITRAGE_SIMPLE,
            1, 2, 3, 5, 6 => null,
        };
    }
}
