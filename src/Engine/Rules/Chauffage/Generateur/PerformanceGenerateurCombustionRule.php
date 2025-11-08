<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformanceGenerateurCombustionRule extends PerformanceGenerateurRule
{
    public function supports(): bool
    {
        return $this->energie_generateur()->is_combustible()
            && false === $this->type_generateur()->is_poele_insert()
            && false === $this->type_generateur()->is_radiateur_gaz()
            && false === $this->generateur_multi_batiment();
    }

    /** @inheritDoc */
    public function scop(): ?float
    {
        return null;
    }
}
