<?php

namespace App\Domain\Enveloppe\Baie\Vitrage;

enum NatureGazLame: string
{
    case AIR = 'air';
    case ARGON = 'argon';
    case KRYPTON = 'krypton';

    public static function try_from_enum_type_gaz_lame_id(int $id): ?self
    {
        return match ($id) {
            1 => self::AIR,
            2 => self::ARGON,
            default => null,
        };
    }
}
