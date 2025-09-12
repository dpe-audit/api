<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementCombustion;

use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;
use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rules\Chauffage\Rendement\RendementCombustion\TauxCharge;

final class RendementChaudiereBasseTemperatureRule extends RendementChaudiereRule
{
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

        $qp = 100 - ($rpint + 0.1 * (40 - $tfonc));
        $qp /= $rpint + 0.1 * (40 - $tfonc);
        $qp *= 0.3 * $pn;
        return $qp;
    }

    public static function supports(SystemeInput $item): bool
    {
        return ($item->generateur()->type()->is_chaudiere() || $item->generateur()->pac_hybride())
            && $item->generateur()->energie()->is_combustible()
            && ModeCombustion::BASSE_TEMPERATURE === $item->generateur()->mode_combustion()
            && false === $item->generateur()->energie()->is_bois()
            && false === $item->generateur()->generateur_multi_batiment();
    }
}
