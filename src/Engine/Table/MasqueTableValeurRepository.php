<?php

namespace App\Engine\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\ConfigurationMasque;
use App\Domain\Enveloppe\Masque\SecteurMasque;

interface MasqueTableValeurRepository
{
    public function fe1(
        ConfigurationMasque $configuration_masque,
        ?Orientation $orientation_facade,
        ?float $avancee_masque,
    ): ?float;

    public function fe2(
        ConfigurationMasque $configuration_masque,
        Orientation $orientation_facade,
        float $hauteur_masque_alpha,
    ): ?float;

    public function omb(
        ConfigurationMasque $configuration_masque,
        SecteurMasque $secteur,
        Orientation $orientation_facade,
        float $hauteur_masque_alpha,
    ): ?float;
}
