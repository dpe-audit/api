<?php

namespace App\Engine\Table;

use App\Domain\Enveloppe\PontThermique\Liaison\{TypeIsolation, TypeLiaison, TypePose};

interface PontThermiqueTableValeurRepository
{
    public function kpt(
        TypeLiaison $type_liaison,
        bool $isolation_mur,
        ?bool $isolation_plancher,
        TypeIsolation $type_isolation_mur,
        ?TypeIsolation $type_isolation_plancher,
        ?TypePose $type_pose,
        ?bool $presence_retour_isolation,
        ?float $largeur_dormant,
    ): ?float;
}
