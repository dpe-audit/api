<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementCombustion;

use App\Engine\Input\Chauffage\SystemeInput;

final class RendementRadiateurGazRule extends RendementCombustionRule
{
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

    public static function supports(SystemeInput $item): bool
    {
        return $item->generateur()->type()->is_radiateur_gaz()
            && $item->generateur()->energie()->is_combustible();
    }
}
