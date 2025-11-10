<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Engine\Rules\Ecs\{PerformanceGenerateurRule, PerformanceSystemeRule};

final class PerformancePacRule extends PerformanceSystemeRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_pac() && false === $this->generateur_multi_batiment();
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
