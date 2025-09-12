<?php

namespace App\Database\Local\Table;

use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Enveloppe\PlancherBas\Position\Mitoyennete;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;
use App\Engine\Table\PlancherBasTableValeurRepository;
use App\Utils\Math;

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
        $_2sp = \round(2 * $surface / $perimetre);

        $records = $this->db->repository('plancher_bas.ue')
            ->createQuery()
            ->and('mitoyennete', $mitoyennete)
            ->andCompareTo('annee_construction', $annee_construction)
            ->getMany()
            ->usort(name: '_2sp', value: $_2sp)
            ->slice(0, 2);

        if (0 === $records->count()) {
            return null;
        }
        if (1 === $records->count()) {
            return $records->first()->floatval('ue');
        }
        return Math::interpolation_lineaire(
            x: $u,
            x1: $records->first()->floatval('u'),
            x2: $records->last()->floatval('u'),
            y1: $records->first()->floatval('ue'),
            y2: $records->last()->floatval('ue'),
        );
    }
}
