<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Domain\Common\Enum\Scenario;
use App\Engine\Rules\Chauffage\Systeme\PerformanceCombustionRule;

final class PerformanceRadiateurGazRule extends PerformanceCombustionRule
{
    public function supports(): bool
    {
        if (false === parent::supports()) {
            return false;
        }
        return $this->type_generateur()->is_radiateur_gaz();
    }

    /**
     * @inheritDoc
     */
    public function qp(Scenario $scenario, TauxCharge $x): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn() * 100;
        $tch = $this->tch_final($scenario, $x);
        return 1.04 * ((100 - $rpn) / $rpn) * $pn * $tch;
    }
}
