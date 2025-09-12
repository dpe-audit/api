<?php

namespace App\Engine\Table;

use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Enveloppe\Mur\Position\Mitoyennete;
use App\Domain\Enveloppe\Mur\TypeMur;

interface MurTableValeurRepository
{
    public function b(Mitoyennete $mitoyennete): ?float;

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
