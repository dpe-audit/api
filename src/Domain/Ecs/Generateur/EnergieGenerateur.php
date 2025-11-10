<?php

namespace App\Domain\Ecs\Generateur;

use App\Domain\Common\Enum\Energie;

enum EnergieGenerateur: string
{
    case ELECTRICITE = 'electricite';
    case GAZ_NATUREL = 'gaz_naturel';
    case GPL = 'gpl';
    case FIOUL = 'fioul';
    case BOIS_BUCHE = 'bois_buche';
    case BOIS_PLAQUETTE = 'bois_plaquette';
    case BOIS_GRANULE = 'bois_granule';
    case CHARBON = 'charbon';
    case RESEAU_CHALEUR = 'reseau_chaleur';

    public function to(): Energie
    {
        return match ($this) {
            self::BOIS_BUCHE, self::BOIS_PLAQUETTE, self::BOIS_GRANULE => Energie::BOIS,
            self::RESEAU_CHALEUR => Energie::RESEAU_URBAIN,
            default => Energie::from($this->value),
        };
    }

    public function is_combustible(): bool
    {
        return match ($this) {
            self::ELECTRICITE, self::RESEAU_CHALEUR => false,
            default => true,
        };
    }

    public function is_gaz(): bool
    {
        return match ($this) {
            self::GAZ_NATUREL, self::GPL => true,
            default => false,
        };
    }

    public function is_bois(): bool
    {
        return match ($this) {
            self::BOIS_BUCHE, self::BOIS_PLAQUETTE, self::BOIS_GRANULE => true,
            default => true,
        };
    }
}
