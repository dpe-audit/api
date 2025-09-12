<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementCombustion;

use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rules\Chauffage\Rendement\RendementCombustion\TauxCharge;

final class RendementChaudiereBoisRule extends RendementChaudiereRule
{
    /**
     * @inheritDoc
     */
    public function qp(TauxCharge $x): float
    {
        $QP0 = $this->qp0();
        $QP50 = $this->qp50();
        $QP100 = $this->qp100();
        $tch = $this->tch_final($x);

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
        $rpint = $this->rpint();
        return 0.5 * $pn * ((100 - $rpint) / $rpint);
    }

    /**
     * Pertes de charge à 100% de puissance
     */
    public function qp100(): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn();
        return $pn * ((100 - $rpn) / $rpn);
    }

    public static function supports(SystemeInput $item): bool
    {
        return ($item->generateur()->type()->is_chaudiere() || $item->generateur()->pac_hybride())
            && $item->generateur()->energie()->is_combustible()
            && $item->generateur()->energie()->is_bois()
            && false === $item->generateur()->generateur_multi_batiment();
    }
}
