<?php

namespace App\Domain\Chauffage\Emetteur;

use App\Domain\Chauffage\Generateur\TypeGenerateur;

enum TypeEmission: string
{
    case AIR_SOUFFLE = 'air_souffle';
    case PLANCHER_CHAUFFANT = 'plancher_chauffant';
    case PLAFOND_CHAUFFANT = 'plafond_chauffant';
    case RADIATEUR = 'radiateur';

    public static function from_type_emetteur(TypeEmetteur $type_emetteur): self
    {
        return match ($type_emetteur) {
            TypeEmetteur::PLANCHER_CHAUFFANT => self::PLANCHER_CHAUFFANT,
            TypeEmetteur::PLAFOND_CHAUFFANT => self::PLAFOND_CHAUFFANT,
            TypeEmetteur::RADIATEUR_MONOTUBE,
            TypeEmetteur::RADIATEUR_BITUBE,
            TypeEmetteur::RADIATEUR => self::RADIATEUR,
        };
    }

    public static function from_type_generateur(TypeGenerateur $type_generateur): self
    {
        return match ($type_generateur) {
            TypeGenerateur::GENERATEUR_AIR_CHAUD,
            TypeGenerateur::GENERATEUR_AIR_CHAUD,
            TypeGenerateur::PAC_AIR_AIR => self::AIR_SOUFFLE,
            default => self::RADIATEUR,
        };
    }
}
