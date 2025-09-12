<?php

namespace App\Domain\Ventilation\Generateur;

enum TypeGenerateur: string
{
    case VMC_SIMPLE_FLUX = 'vmc_simple_flux';
    case VMC_SIMPLE_FLUX_GAZ = 'vmc_simple_flux_gaz';
    case VMC_BASSE_PRESSION = 'vmc_basse_pression';
    case VMC_DOUBLE_FLUX = 'vmc_double_flux';
    case VMI = 'vmi';
    case VENTILATION_HYBRIDE = 'ventilation_hybride';
    case VENTILATION_MECANIQUE = 'ventilation_mecanique';
    case PUIT_CLIMATIQUE = 'puit_climatique';
    case VMR = 'vmr';

    public function is_generateur_collectif(): ?bool
    {
        return match ($this) {
            self::VMR => false,
            default => null,
        };
    }

    public function is_vmc(): bool
    {
        return match ($this) {
            self::VMC_SIMPLE_FLUX, self::VMC_SIMPLE_FLUX_GAZ, self::VMC_BASSE_PRESSION, self::VMC_DOUBLE_FLUX => true,
            default => false,
        };
    }
}
