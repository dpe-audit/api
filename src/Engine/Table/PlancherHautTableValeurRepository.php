<?php

namespace App\Engine\Table;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Enveloppe\PlancherHaut\{Configuration, TypePlancherHaut};

interface PlancherHautTableValeurRepository extends ParoiTableValeurRepository
{
    public function u0(?TypePlancherHaut $type_structure): ?float;

    public function u(
        ZoneClimatique $zone_climatique,
        Configuration $configuration,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?float;
}
