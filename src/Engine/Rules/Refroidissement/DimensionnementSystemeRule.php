<?php

namespace App\Engine\Rules\Refroidissement;

abstract class DimensionnementSystemeRule extends CommonSystemeRule
{
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
