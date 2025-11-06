<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\Generateur;
use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformanceGenerateurCombustionRule extends PerformanceGenerateurRule
{
    public static function supports(Generateur $entity): bool
    {
        return $entity->energie()?->is_combustible()
            && false === $entity->type()?->is_poele_insert()
            && false === $entity->type()?->is_radiateur_gaz()
            && false === $entity->position()->generateur_multi_batiment;
    }

    /** @inheritDoc */
    public function scop(): ?float
    {
        return null;
    }
}
