<?php

namespace App\Engine\Table;

use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Lnc\Baie\Materiau;
use App\Domain\Enveloppe\Lnc\Baie\TypeVitrage;
use App\Domain\Enveloppe\Lnc\TypeLnc;

interface LncTableValeurRepository
{
    public function uvue(TypeLnc $type_lnc): ?float;

    public function b(
        float $uvue,
        bool $isolation_aiu,
        bool $isolation_aue,
        float $aiu,
        float $aue,
    ): ?float;

    public function bver(
        ZoneClimatique $zone_climatique,
        Orientation $orientation,
        bool $isolation_paroi,
    ): ?float;

    public function t(
        Materiau $materiau,
        TypeVitrage $type_vitrage,
        bool $presence_rupteur_pont_thermique,
    ): ?float;
}
