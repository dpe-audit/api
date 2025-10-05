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
    case RESEAU_URBAIN = 'reseau_urbain';

    public static function from_enum_energie_id(int $id): ?self
    {
        return match ($id) {
            1 => self::ELECTRICITE,
            2 => self::GAZ_NATUREL,
            3 => self::FIOUL,
            4 => self::BOIS,
            5 => self::BOIS,
            6 => self::BOIS,
            7 => self::BOIS,
            8 => self::RESEAU_URBAIN,
            9 => self::GPL,
            10 => self::GPL,
            11 => self::CHARBON,
            12 => self::ELECTRICITE,
            13 => self::GPL,
            15 => self::RESEAU_URBAIN,
            default => null,
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
            self::RESEAU_URBAIN => 1,
        };
    }

    /**
     * Facteur de conversion en énergie primaire
     */
    public function facteur_energie_primaire(): float
    {
        return match ($this) {
            self::ELECTRICITE => 1.9,
            default => 1
        };
    }

    /**
     * Facteur d'émission de gaz à effet de serre en kgCO2/kWh
     */
    public function facteur_eges(Usage $usage): float
    {
        return match ($this) {
            self::ELECTRICITE => match ($usage) {
                Usage::CHAUFFAGE => 0.079,
                Usage::ECS => 0.065,
                Usage::REFROIDISSEMENT => 0.064,
                Usage::ECLAIRAGE => 0.069,
                Usage::AUXILIAIRE => 0.079,
            },
            self::GAZ_NATUREL => 0.227,
            self::GPL => 0.272,
            self::FIOUL => 0.324,
            self::BOIS => 0.03,
            self::CHARBON => 0.385,
            self::RESEAU_URBAIN => 0.385,
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
