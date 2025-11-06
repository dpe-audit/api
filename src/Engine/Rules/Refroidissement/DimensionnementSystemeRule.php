<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Refroidissement\Systeme\Systeme;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Systeme>
 */
abstract class DimensionnementSystemeRule extends RuleIterator
{
    // * Données d'entrées

    public function nombre_systemes(): int
    {
        return $this->item()->installation()->systemes()->count();
    }

    // * Données intermédiaires

    public function rdim_installation(): float
    {
        return $this->requireIterator(PerformanceInstallationRule::class, $this->item()->installation())->rdim();
    }

    // * Données calculées

    /**
     * Ratio de dimensionnement du système de refroidissement
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return 1 / $this->nombre_systemes() * $this->rdim_installation();
        });
    }
}
