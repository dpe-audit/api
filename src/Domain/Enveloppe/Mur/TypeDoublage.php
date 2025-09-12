<?php

namespace App\Domain\Enveloppe\Mur;

enum TypeDoublage: string
{
    case SANS_DOUBLAGE = 'sans_doublage';
    case INDETERMINE = 'indetermine';
    case LAME_AIR_INFERIEUR_15MM = 'lame_air_inferieur_15mm';
    case LAME_AIR_SUPERIEUR_15MM = 'lame_air_superieur_15mm';
    case MATERIAUX_CONNU = 'materiaux_connu';

    public function resistance_thermique_doublage(): float
    {
        return match ($this) {
            self::SANS_DOUBLAGE => 0,
            self::INDETERMINE, self::LAME_AIR_INFERIEUR_15MM => 0.1,
            self::LAME_AIR_SUPERIEUR_15MM, self::MATERIAUX_CONNU => 0.21,
        };
    }
}
