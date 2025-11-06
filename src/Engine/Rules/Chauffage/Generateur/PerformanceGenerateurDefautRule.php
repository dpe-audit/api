<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\Generateur;
use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformanceGenerateurDefautRule extends PerformanceGenerateurRule
{
    public static function supports(Generateur $entity): bool
    {
        return null === $entity->type() && false === $entity->position()->generateur_multi_batiment;
    }

    /** @inheritDoc */
    public function scop(): ?float
    {
        return null;
    }
}
