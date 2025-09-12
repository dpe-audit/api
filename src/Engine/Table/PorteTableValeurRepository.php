<?php

namespace App\Engine\Table;

use App\Domain\Enveloppe\Porte\Materiau;
use App\Domain\Enveloppe\Porte\Position\Mitoyennete;
use App\Domain\Enveloppe\Porte\Vitrage\TypeVitrage;

interface PorteTableValeurRepository
{
    public function b(Mitoyennete $mitoyennete): ?float;

    public function u(
        bool $presence_sas,
        bool $isolation,
        Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?float $taux_vitrage,
    ): ?float;
}
