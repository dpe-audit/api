<?php

namespace App\Domain\Chauffage\Emetteur;

/**
 * TODO: Vérifier les autres types d'émetteurs (cf 15.2.1)
 */
enum TypeEmetteur: string
{
    case PLANCHER_CHAUFFANT = 'plancher_chauffant';
    case PLAFOND_CHAUFFANT = 'plafond_chauffant';
    case RADIATEUR_MONOTUBE = 'radiateur_monotube';
    case RADIATEUR_BITUBE = 'radiateur_bitube';
    case RADIATEUR = 'radiateur';

    /**
     * Pertes de charge de l'émetteur exprimées en kPa
     */
    public function pertes_charge(): float
    {
        return match ($this) {
            self::PLANCHER_CHAUFFANT => 15,
            self::PLAFOND_CHAUFFANT => 15,
            self::RADIATEUR_MONOTUBE => 30,
            self::RADIATEUR_BITUBE => 10,
            self::RADIATEUR => 10,
        };
    }

    public function fcot(): float
    {
        return match ($this) {
            self::PLANCHER_CHAUFFANT => 0.156,
            default => 0.802,
        };
    }
}
