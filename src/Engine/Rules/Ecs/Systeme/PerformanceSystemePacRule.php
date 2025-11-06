<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\Rules\Ecs\{PerformanceGenerateurRule, PerformanceSystemeRule};

final class PerformanceSystemePacRule extends PerformanceSystemeRule
{
    public static function supports(Systeme $entity): bool
    {
        return $entity->generateur()->type()?->is_pac()
            && false === $entity->generateur()->position()->generateur_multi_batiment;
    }

    // * Données intermédiaire

    public function cop(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->cop();
    }

    /**
     * @inheritDoc
     */
    public function rgs(): float
    {
        return $this->get('rgs', function (): float {
            return $this->cop() ?? throw new \DomainException("Valeur COP non calculée");
        });
    }
}
