<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Common\Enum\Scenario;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;

final class PerformanceReseauChaleurRule extends PerformanceSystemeRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_reseau_chaleur() || $this->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function rg(Scenario $scenario): float
    {
        return $this->get('rg', fn(): float => 0.97);
    }
}
