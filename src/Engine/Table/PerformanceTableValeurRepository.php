<?php

namespace App\Engine\Table;

use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Ressource\{EtiquetteClimat, EtiquetteEnergie};

interface PerformanceTableValeurRepository
{
    public function etiquette_energie(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $cep,
        float $eges,
    ): ?EtiquetteEnergie;

    public function etiquette_climat(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $eges,
    ): ?EtiquetteClimat;
}
