<?php

namespace App\Engine\Table;

use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Enveloppe\PlancherHaut\{Configuration, TypePlancherHaut};
use App\Domain\Enveloppe\PlancherHaut\Position\Mitoyennete;

interface PlancherHautTableValeurRepository
{
    public function b(Mitoyennete $mitoyennete): ?float;

    public function u0(?TypePlancherHaut $type_structure): ?float;

    public function u(
        ZoneClimatique $zone_climatique,
        Configuration $configuration,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?float;
}
