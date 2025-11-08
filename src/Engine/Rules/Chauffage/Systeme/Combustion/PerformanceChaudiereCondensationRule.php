<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;

final class PerformanceChaudiereCondensationRule extends PerformanceChaudiereRule
{
    public function supports(): bool
    {
        return parent::supports()
            && false === $this->energie_generateur()->is_bois()
            && ModeCombustion::CONDENSATION === $this->mode_combustion();
    }

    /**
     * @inheritDoc
     */
    public function qp(TauxCharge $x): float
    {
        $QP0 = $this->qp0();
        $QP30 = $this->qp30();
        $QP100 = $this->qp100();
        $tch = $this->tch_final($x);

        if ($x === TauxCharge::TCH5) {
            $QP15 = $this->qp(TauxCharge::TCH15);
            return ((($QP15 - 0.15 * $QP0) * $tch) / 0.15) + 0.15 * $QP0;
        }
        if ($x === TauxCharge::TCH15) {
            return $QP30 / 2;
        }
        if ($x === TauxCharge::TCH25) {
            $QP15 = $this->qp(TauxCharge::TCH15);
            return ((($QP30 - $QP15) * $tch) / 0.15) + $QP15 * ((($QP30 - $QP15) * 0.15) / 0.15);
        }
        return ((($QP100 - $QP30) * $tch) / 0.7) + $QP30 - ((($QP100 - $QP30) * 0.3) / 0.7);
    }

    /**
     * Pertes de charge à 30% de puissance exprimées en kW
     */
    public function qp30(): float
    {
        $pn = $this->pn();
        $rpint = $this->rpint();
        $tfonc = $this->regulation() ? $this->tfonc30() : $this->tfonc100();

        $qp = 100 - ($rpint + 0.2 * (33 - $tfonc));
        $qp /= $rpint + 0.2 * (33 - $tfonc);
        $qp *= 0.3 * $pn;
        return $qp;
    }
}
