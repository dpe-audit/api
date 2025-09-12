<?php

namespace App\Domain\Common\Enum;

enum Energie: string
{
    case ELECTRICITE = 'electricite';
    case GAZ_NATUREL = 'gaz_naturel';
    case GPL = 'gpl';
    case FIOUL = 'fioul';
    case BOIS = 'bois';
    case CHARBON = 'charbon';
    case RESEAU_CHALEUR = 'reseau_chaleur';
    case RESEAU_FROID = 'reseau_froid';

    public static function from_enum_energie_id(int $id): self
    {
        return match ($id) {
            1, 12 => self::ELECTRICITE,
            2 => self::GAZ_NATUREL,
            3 => self::FIOUL,
            4, 5, 6, 7 => self::BOIS,
            8 => self::RESEAU_CHALEUR,
            9, 10, 13 => self::GPL,
            11 => self::CHARBON,
            15 => self::RESEAU_FROID,
        };
    }

    /**
     * Coefficient de conversion en PCI/PCS
     */
    public function coefficient_conversion_pcs(): float
    {
        return match ($this) {
            self::ELECTRICITE => 1,
            self::GAZ_NATUREL => 1.11,
            self::GPL => 1.09,
            self::FIOUL => 1.07,
            self::CHARBON => 1.04,
            self::BOIS => 1.08,
            self::RESEAU_CHALEUR => 1,
            self::RESEAU_FROID => 1,
        };
    }

    /**
     * Facteur de conversion en énergie primaire
     */
    public function facteur_energie_primaire(): float
    {
        return match ($this) {
            self::ELECTRICITE => 2.3,
            default => 1
        };
    }

    public function is_combustible(): bool
    {
        return \in_array($this, [
            self::GAZ_NATUREL,
            self::GPL,
            self::FIOUL,
            self::BOIS,
            self::CHARBON,
        ]);
    }
}
