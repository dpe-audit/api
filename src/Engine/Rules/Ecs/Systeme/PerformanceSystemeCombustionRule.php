<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Engine\Rules\Ecs\PerformanceSystemeRule;

abstract class PerformanceSystemeCombustionRule extends PerformanceSystemeRule
{
    public function supports(): bool
    {
        return $this->energie_generateur()->is_combustible() && false === $this->generateur_multi_batiment();
    }
}
