<?php

namespace App\Engine\Table;

use App\Domain\Batiment\ZoneClimatique;

interface RefroidissementTableValeurRepository
{
    public function eer(
        ZoneClimatique $zone_climatique,
        int $annee_installation_generateur,
    ): ?float;
}
