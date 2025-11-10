<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Domain\Common\Enum\Scenario;
use App\Domain\Ecs\Systeme\Reseau\IsolationReseau;
use App\Engine\Rules\Ecs\PerformanceSystemeRule;

final class PerformanceReseauChaleurRule extends PerformanceSystemeRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_reseau_chaleur() || $this->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function rgs(Scenario $scenario): float
    {
        return match ($this->isolation_reseau()) {
            IsolationReseau::ISOLE => 0.9,
            IsolationReseau::NON_ISOLE => 0.75,
            default => 0.75,
        };
    }
}
