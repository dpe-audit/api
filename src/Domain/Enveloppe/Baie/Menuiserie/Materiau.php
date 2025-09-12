<?php

namespace App\Domain\Enveloppe\Baie\Menuiserie;

enum Materiau: string
{
    case BOIS = 'bois';
    case BOIS_METAL = 'bois_metal';
    case PVC = 'pvc';
    case METAL = 'metal';

    public static function from_enum_type_materiaux_menuiserie_id(int $id): ?self
    {
        return match ($id) {
            1, 2 => null,
            3 => self::BOIS,
            4 => self::BOIS_METAL,
            5 => self::PVC,
            6 => self::METAL,
            7 => self::METAL,
        };
    }
}
