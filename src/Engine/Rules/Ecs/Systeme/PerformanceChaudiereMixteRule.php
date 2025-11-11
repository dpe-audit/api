<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Domain\Common\Enum\Scenario;

final class PerformanceChaudiereMixteRule extends PerformanceSystemeCombustionRule
{
    public function supports(): bool
    {
        if (false === parent::supports()) {
            return false;
        }
        return false === ($this->type_generateur()->is_chauffe_eau() && $this->energie_generateur()->is_gaz());
    }

    /**
     * @inheritDoc
     */
    public function rgs(Scenario $scenario): float
    {
        return $this->get(self::implode(['rgs', $scenario]), function () use ($scenario): float {
            $becs = $this->becs($scenario) * 1000;
            $pertes = $this->pertes_stockage();
            $rpn = $this->rpn();
            $qp0 = $this->qp0();
            $pveilleuse = $this->pveilleuse();

            $rgs = 1 / $rpn;
            $rgs += (1790 * $qp0 + $pertes) / $becs;
            $rgs += 6970 * (0.5 * $pveilleuse / $becs);
            $rgs = 1 / $rgs;
            return $rgs;
        });
    }
}
