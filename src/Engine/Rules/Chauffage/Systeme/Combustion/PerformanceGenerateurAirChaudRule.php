<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Domain\Common\Enum\Scenario;
use App\Engine\Rules\Chauffage\Systeme\PerformanceCombustionRule;

final class PerformanceGenerateurAirChaudRule extends PerformanceCombustionRule
{
    public function supports(): bool
    {
        if (false === parent::supports()) {
            return false;
        }
        return $this->type_generateur()->is_generateur_air_chaud();
    }

    public function qp(Scenario $scenario, TauxCharge $x): float
    {
        $QP0 = $this->qp0() / 1000;
        $QP50 = $this->qp50();
        $QP100 = $this->qp100();
        $tch = $this->tch_final($scenario, $x);

        return $x->value < 50
            ? ((($QP50 - 0.15 * $QP0) * $tch) / 0.5) + 0.15 * $QP0
            : ((($QP100 - $QP50) * $tch) / 0.5) + 2 * $QP50 - $QP100;
    }

    /**
     * Pertes de charge à 50% de puissance
     */
    public function qp50(): float
    {
        $pn = $this->pn();
        $rpint = $this->rpint() * 100;
        return 0.5 * $pn * ((100 - $rpint) / $rpint);
    }

    /**
     * Pertes de charge à 100% de puissance
     */
    public function qp100(): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn() * 100;
        return $pn * ((100 - $rpn) / $rpn);
    }
}
