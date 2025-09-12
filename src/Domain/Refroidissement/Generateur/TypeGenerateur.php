<?php

namespace App\Domain\Refroidissement\Generateur;

enum TypeGenerateur: string
{
    case PAC_AIR_AIR = 'pac_air_air';
    case PAC_AIR_EAU = 'pac_air_eau';
    case PAC_EAU_EAU = 'pac_eau_eau';
    case PAC_EAU_GLYCOLEE_EAU = 'pac_eau_glycolee_eau';
    case PAC_GEOTHERMIQUE = 'pac_geothermique';
    case RESEAU_FROID = 'reseau_froid';
    case AUTRE_SYSTEME_THERMODYNAMIQUE = 'autre_systeme_thermodynamique';
    case AUTRE = 'autre';

    public function is_climatiseur(): bool
    {
        return $this === self::AUTRE;
    }

    public function is_pac(): bool
    {
        return \in_array($this, [
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::AUTRE_SYSTEME_THERMODYNAMIQUE,
        ]);
    }

    public function is_reseau_froid(): bool
    {
        return $this === self::RESEAU_FROID;
    }

    public function is_thermodynamique(): bool
    {
        return \in_array($this, [
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::AUTRE_SYSTEME_THERMODYNAMIQUE,
        ]);
    }
}
