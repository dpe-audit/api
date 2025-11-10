<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Common\Enum\Scenario;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;

final class PerformanceGenerateurEffetJouleRule extends PerformanceSystemeRule
{
    public  function supports(): bool
    {
        return $this->energie_generateur()->is_electricite()
            && false === $this->type_generateur()->is_pac()
            && false === $this->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function rg(Scenario $scenario): float
    {
        return $this->get('rg', function (): float {
            return $this->type_generateur()->is_chaudiere() ? 0.97 : 1;
        });
    }
}
