<?php

namespace App\Engine\Table;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;

interface PlancherBasTableValeurRepository extends ParoiTableValeurRepository
{
    public function u0(?TypePlancherBas $type_structure): ?float;

    public function ue(
        Mitoyennete $mitoyennete,
        int $annee_construction,
        float $perimetre,
        float $surface,
        float $u,
    ): ?float;

    public function u(
        ZoneClimatique $zone_climatique,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?float;
}
