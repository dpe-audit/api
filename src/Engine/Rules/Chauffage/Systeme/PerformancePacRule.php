<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Common\Enum\Scenario;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;

final class PerformancePacRule extends PerformanceSystemeRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_pac()
            && null === $this->bienergie_generateur()
            && false === $this->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function rg(Scenario $scenario): float
    {
        return $this->get('rg', function (): float {
            return $this->scop() ?? throw new \DomainException('SCOP non calculée');
        });
    }
}
