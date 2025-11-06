<?php

namespace App\Engine\Table;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Enveloppe\Mur\TypeMur;

interface MurTableValeurRepository extends ParoiTableValeurRepository
{
    public function u0(
        int $annee_construction,
        ?TypeMur $type_structure,
        ?float $epaisseur_structure,
    ): ?float;

    public function u(
        ZoneClimatique $zone_climatique,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?float;
}
