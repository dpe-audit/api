<?php

namespace App\Domain\Ventilation\Installation;

enum TypeVentilation: string
{
    case VENTILATION_MECANIQUE =
    'ventilation_mecanique';
    case VENTILATION_NATURELLE_OUVERTURE_FENETRES =
    'ventilation_naturelle_ouverture_fenetres';
    case VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES =
    'ventilation_naturelle_entrees_air_hautes_basses';
    case VENTILATION_NATURELLE_CONDUIT =
    'ventilation_naturelle_conduit';
    case VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES =
    'ventilation_naturelle_conduit_entrees_air_hygroreglables';

    public function is_naturelle(): bool
    {
        return match ($this) {
            self::VENTILATION_NATURELLE_OUVERTURE_FENETRES,
            self::VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES,
            self::VENTILATION_NATURELLE_CONDUIT,
            self::VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES => true,
            default => false,
        };
    }

    public function is_mecanique(): bool
    {
        return match ($this) {
            self::VENTILATION_MECANIQUE => true,
            default => false,
        };
    }
}
