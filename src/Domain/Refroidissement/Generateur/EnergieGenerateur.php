<?php

namespace App\Domain\Refroidissement\Generateur;

use App\Domain\Common\Enum\Energie;

enum EnergieGenerateur: string
{
    case ELECTRICITE = 'electricite';
    case GAZ_NATUREL = 'gaz_naturel';
    case GPL = 'gpl';
    case RESEAU_FROID = 'reseau_froid';

    public function to(): Energie
    {
        return match ($this) {
            self::RESEAU_FROID => Energie::RESEAU_URBAIN,
            default => Energie::from($this->value),
        };
    }
}
