<?php

namespace App\Engine\Rules\Ecs\Generateur;

use App\Domain\Ecs\Generateur\Generateur;
use App\Engine\Rules\Ecs\PerformanceGenerateurRule;

final class PerformanceGenerateurAutresRule extends PerformanceGenerateurRule
{
    public static function supports(Generateur $entity): bool
    {
        return PerformanceGenerateurPacRule::supports($entity) === false
            && PerformanceGenerateurCombustionRule::supports($entity) === false;
    }
}
