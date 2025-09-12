<?php

namespace App\Engine\Table;

use App\Domain\Batiment\TypeBatiment;

interface EnveloppeTableValeurRepository
{
    public function q4pa_conv(
        TypeBatiment $type_batiment,
        int $annee_construction,
        bool $presence_joints_menuiserie,
        bool $isolation_murs_plafonds,
    ): ?float;
}
