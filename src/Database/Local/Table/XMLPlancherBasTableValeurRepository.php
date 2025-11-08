<?php

namespace App\Database\Local\Table;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;
use App\Engine\Table\PlancherBasTableValeurRepository;
use App\Utils\Interpolation;

final class XMLPlancherBasTableValeurRepository extends XMLParoiTableValeurRepository implements PlancherBasTableValeurRepository
{
    public function u0(?TypePlancherBas $type_structure): ?float
    {
        return $this->db->repository('plancher_bas.u0')
            ->createQuery()
            ->and('type_structure', $type_structure)
            ->getOne()
            ?->floatval('u0');
    }

    public function u(
        ZoneClimatique $zone_climatique,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?float {
        return $this->db->repository('plancher_bas.u')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne()
            ?->floatval('u');
    }

    public function ue(
        Mitoyennete $mitoyennete,
        int $annee_construction,
        float $perimetre,
        float $surface,
        float $u,
    ): ?float {
        $points = $this->db->repository('plancher_bas.ue')
            ->createQuery()
            ->and('mitoyennete', $mitoyennete)
            ->andCompareTo('annee_construction', $annee_construction)
            ->getMany()
            ->points('u', '_2s_p', 'ue');

        $_2s_p = \round(2 * $surface / $perimetre);
        $f = new Interpolation($points, Interpolation::METHOD_BILENAIRE);
        return $f->interpolationBilenaire($u, $_2s_p);
    }
}
