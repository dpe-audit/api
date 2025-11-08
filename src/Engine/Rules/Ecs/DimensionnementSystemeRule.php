<?php

namespace App\Engine\Rules\Ecs;

abstract class DimensionnementSystemeRule extends CommonSystemeRule
{
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
