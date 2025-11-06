<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Systeme>
 */
abstract class DimensionnementSystemeRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function nombre_systemes(): int
    {
        return $this->item()->installation()->systemes()->count();
    }

    // * Données intermédiaires

    public function rdim_installation(): float
    {
        return $this->requireIterator(PerformanceInstallationRule::class, $this->item()->installation())->rdim();
    }

    // * Données de sortie

    /**
     * Ratio de dimensionnement du système d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return 1 / $this->nombre_systemes() * $this->rdim_installation();
        });
    }
}
