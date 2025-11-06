<?php

namespace App\Database\Local\Table;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Lnc\Baie\Materiau;
use App\Domain\Enveloppe\Lnc\Baie\TypeVitrage;
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Engine\Table\LncTableValeurRepository;

final class XMLLncTableValeurRepository implements LncTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function uvue(TypeLnc $type_lnc): ?float
    {
        return $this->db->repository('lnc.uvue')
            ->createQuery()
            ->and('type_local_non_chauffe', $type_lnc)
            ->getOne()
            ?->floatval('uvue');
    }

    public function b(
        float $uvue,
        bool $isolation_aiu,
        bool $isolation_aue,
        float $aiu,
        float $aue,
    ): ?float {
        $aiu_aue = $aiu / $aue;
        return $this->db->repository('lnc.b')
            ->createQuery()
            ->and('uvue', $uvue)
            ->and('isolation_aiu', $isolation_aiu)
            ->and('isolation_aue', $isolation_aue)
            ->andCompareTo('aiu_aue', $aiu_aue)
            ->getOne()
            ?->floatval('b');
    }

    public function bver(
        ZoneClimatique $zone_climatique,
        Orientation $orientation,
        bool $isolation_paroi
    ): ?float {
        return $this->db->repository('lnc.bver')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('orientation', $orientation)
            ->and('isolation_paroi', $isolation_paroi)
            ->getOne()
            ?->floatval('bver');
    }

    public function t(
        Materiau $materiau,
        TypeVitrage $type_vitrage,
        bool $presence_rupteur_pont_thermique,
    ): ?float {
        return $this->db->repository('lnc.t')
            ->createQuery()
            ->and('type_vitrage', $type_vitrage)
            ->and('materiau', $materiau)
            ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique)
            ->getOne()
            ?->floatval('t');
    }
}
