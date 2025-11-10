<?php

namespace App\Engine\Rules\Ecs\Systeme;

final class PerformanceChauffeEauGazRule extends PerformanceSystemeCombustionRule
{
    public function supports(): bool
    {
        if (false === parent::supports()) {
            return false;
        }
        return $this->type_generateur()->is_chauffe_eau()
            && $this->energie_generateur()->is_gaz()
            && $this->volume_stockage() == 0;
    }

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            $becs = $this->becs();
            $rpn = $this->rpn();
            $qp0 = $this->qp0();
            $pveilleuse = $this->pveilleuse();

            $rg = 1 / $rpn;
            $rg += 1790 * ($qp0 / $becs);
            $rg += 6970 * ($pveilleuse / $becs);
            return 1 / $rg;
        });
    }
}
