<?php

namespace App\Database\Local\Table;

use App\Engine\Table\PorteTableValeurRepository;
use App\Domain\Enveloppe\Porte\Materiau;
use App\Domain\Enveloppe\Porte\Vitrage\TypeVitrage;

final class XMLPorteTableValeurRepository extends XMLParoiTableValeurRepository implements PorteTableValeurRepository
{
    public function u(
        bool $presence_sas,
        bool $isolation,
        Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?float $taux_vitrage,
    ): ?float {
        return $this->db->repository('porte.u')
            ->createQuery()
            ->and('presence_sas', $presence_sas)
            ->and('isolation', $isolation)
            ->and('materiau', $materiau, false)
            ->and('type_vitrage', $type_vitrage, false)
            ->andCompareTo('taux_vitrage', $taux_vitrage)
            ->getOne()
            ?->floatval('u');
    }
}
