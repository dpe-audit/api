<?php

namespace App\Domain\Enveloppe\Masque;

enum ConfigurationMasque: string
{
    case HOMOGENE = 'homogene';
    case NON_HOMOGENE = 'non_homogene';
    case FOND_BALCON = 'fond_balcon';
    case FOND_ET_FLANC_LOGGIAS = 'fond_et_flanc_loggias';
    case BALCON_OU_AUVENT = 'balcon_ou_auvent';
    case PAROI_LATERALE_SANS_OBSTACLE_AU_SUD = 'paroi_laterale_sans_obstacle_au_sud';
    case PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD = 'paroi_laterale_avec_obstacle_au_sud';

    public function is_masque_lointain(): bool
    {
        return match ($this) {
            self::HOMOGENE, self::NON_HOMOGENE => true,
            default => false,
        };
    }

    public function is_masque_proche(): bool
    {
        return !$this->is_masque_lointain();
    }
}
