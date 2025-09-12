<?php

namespace App\Engine\Table;

use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Common\Enum\{Orientation, Mois};
use App\Engine\SollicitationsClimatiques\SollicitationsExterieures;

interface SollicitationsClimatiquesTableValeurRepository
{
    /**
     * @return array<SollicitationsExterieures>
     */
    public function sollicitations_exterieures(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        bool $parois_anciennes_lourdes,
    ): array;

    public function tbase(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
    ): ?float;

    /**
     * @return array{mois: Mois, c1: float}[]
     */
    public function c1(
        ZoneClimatique $zone_climatique,
        float $inclinaison,
        ?Orientation $orientation,
    ): array;
}
