<?php

namespace App\Database\Local\Table;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Enveloppe\PlancherHaut\{Configuration, TypePlancherHaut};
use App\Engine\Table\PlancherHautTableValeurRepository;

final class XMLPlancherHautTableValeurRepository extends XMLParoiTableValeurRepository implements PlancherHautTableValeurRepository
{
    public function u0(?TypePlancherHaut $type_structure): ?float
    {
        return $this->db->repository('plancher_haut.u0')
            ->createQuery()
            ->and('type_structure', $type_structure)
            ->getOne()
            ?->floatval('u0');
    }

    public function u(
        ZoneClimatique $zone_climatique,
        Configuration $configuration,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?float {
        return $this->db->repository('plancher_haut.u')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('configuration', $configuration)
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne()
            ?->floatval('u');
    }
}
