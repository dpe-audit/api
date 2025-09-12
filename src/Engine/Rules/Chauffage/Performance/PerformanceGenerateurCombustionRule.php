<?php

namespace App\Engine\Rules\Chauffage\Performance;

use App\Engine\Input\Chauffage\GenerateurInput;

final class PerformanceGenerateurCombustionRule extends PerformanceGenerateurRule
{
    /** @inheritDoc */
    public function scop(): ?float
    {
        return null;
    }

    public static function supports(GenerateurInput $item): bool
    {
        return $item->energie()->is_combustible()
            && false === $item->type()->is_poele_insert()
            && false === $item->type()->is_radiateur_gaz()
            && false === $item->generateur_multi_batiment();
    }
}
