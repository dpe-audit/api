<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;
use App\Domain\Common\Enum\Scenario;

final class PerformanceChaudiereStandardRule extends PerformanceChaudiereRule
{
    public function supports(): bool
    {
        if (false === parent::supports()) {
            return false;
        }
        return false === $this->energie_generateur()->is_bois()
            && ModeCombustion::STANDARD === $this->mode_combustion();
    }

    /**
     * @inheritDoc
     */
    public function qp(Scenario $scenario, TauxCharge $x): float
    {
        $QP0 = $this->qp0() / 1000;
        $QP30 = $this->qp30();
        $QP100 = $this->qp100();
        $tch = $this->tch_final($scenario, $x);

        return $x->value < 30
            ? ($QP30 - 0.15 * $QP0) * $tch / 0.3 + 0.15 * $QP0
            : ($QP100 - $QP30) * $tch / 0.7 + $QP30 - ($QP100 - $QP30) * 0.3 / 0.7;
    }

    /**
     * Pertes de charge à 30% de puissance exprimées en kW
     */
    public function qp30(): float
    {
        $pn = $this->pn();
        $rpint = $this->rpint() * 100;
        $tfonc = $this->regulation() ? $this->tfonc30() : $this->tfonc100();

        $qp = 100 - ($rpint + 0.1 * (50 - $tfonc));
        $qp /= $rpint + 0.1 * (50 - $tfonc);
        $qp *= 0.3 * $pn;
        return $qp;
    }
}
