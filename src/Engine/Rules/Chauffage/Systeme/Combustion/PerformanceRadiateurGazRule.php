<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Domain\Chauffage\Systeme\Systeme;
use App\Engine\Rules\Chauffage\Systeme\PerformanceCombustionRule;

final class PerformanceRadiateurGazRule extends PerformanceCombustionRule
{
    public static function supports(Systeme $entity): bool
    {
        return $entity->generateur()->type()?->is_radiateur_gaz()
            && $entity->generateur()->energie()?->is_combustible();
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
