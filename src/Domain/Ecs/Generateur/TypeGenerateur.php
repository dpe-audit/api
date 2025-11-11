<?php

namespace App\Domain\Ecs\Generateur;

enum TypeGenerateur: string
{
    case CHAUFFE_EAU = 'chauffe_eau';
    case CHAUDIERE = 'chaudiere';
    case CET_AIR_AMBIANT = 'cet_air_ambiant';
    case CET_AIR_EXTERIEUR = 'cet_air_exterieur';
    case CET_AIR_EXTRAIT = 'cet_air_extrait';
    case PAC_DOUBLE_SERVICE = 'pac_double_service';
    case POELE_BOUILLEUR = 'poele_bouilleur';
    case RESEAU_CHALEUR = 'reseau_chaleur';

    public function is_chauffe_eau(): bool
    {
        return $this === self::CHAUFFE_EAU;
    }

    public function is_chaudiere(): bool
    {
        return \in_array($this, [self::CHAUDIERE, self::POELE_BOUILLEUR]);
    }

    public function is_poele_bouilleur(): bool
    {
        return $this === self::POELE_BOUILLEUR;
    }

    public function is_pac(): bool
    {
        return \in_array($this, [
            self::CET_AIR_AMBIANT,
            self::CET_AIR_EXTERIEUR,
            self::CET_AIR_EXTRAIT,
            self::PAC_DOUBLE_SERVICE,
        ]);
    }

    public function is_pac_double_service(): bool
    {
        return $this === self::PAC_DOUBLE_SERVICE;
    }

    public function is_reseau_chaleur(): bool
    {
        return $this === self::RESEAU_CHALEUR;
    }
}
