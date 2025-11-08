<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Engine\Rules\Chauffage\Systeme\PerformanceCombustionRule;

final class PerformanceRadiateurGazRule extends PerformanceCombustionRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_radiateur_gaz() && $this->energie_generateur()->is_combustible();
    }

    /**
     * @inheritDoc
     */
    public function qp(TauxCharge $x): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn();
        $tch = $this->tch_final($x);
        return 1.04 * ((100 - $rpn) / $rpn) * $pn * $tch;
    }
}
