<?php

namespace App\Database\Local\Table;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Enveloppe\Mur\TypeMur;
use App\Engine\Table\MurTableValeurRepository;

final class XMLMurTableValeurRepository extends XMLParoiTableValeurRepository implements MurTableValeurRepository
{
    public function u0(
        int $annee_construction,
        float $epaisseur_structure,
        ?TypeMur $type_structure,
    ): ?float {
        return $this->db->repository('mur.u0')
            ->createQuery()
            ->and('type_structure', $type_structure)
            ->andCompareTo('epaisseur_structure', $epaisseur_structure)
            ->andCompareTo('annee_construction', $annee_construction)
            ->getOne()
            ?->floatval('u0');
    }

    public function u(
        ZoneClimatique $zone_climatique,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?float {
        return $this->db->repository('mur.u')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne()
            ?->floatval('u');
    }
}
