<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementCombustion;

use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;
use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rules\Chauffage\Rendement\RendementCombustion\TauxCharge;

final class RendementChaudiereStandardRule extends RendementChaudiereRule
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
        $rpint = $this->rpint();
        $tfonc = $this->regulation() ? $this->tfonc30() : $this->tfonc100();

        $qp = 100 - ($rpint + 0.1 * (50 - $tfonc));
        $qp /= $rpint + 0.1 * (50 - $tfonc);
        $qp *= 0.3 * $pn;
        return $qp;
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), function (SystemeInput $item): bool {
            return $item->generateur()->energie()->is_bois()
                && $item->generateur()->mode_combustion() === ModeCombustion::STANDARD;
        });
    }

    public static function supports(SystemeInput $item): bool
    {
        return ($item->generateur()->type()->is_chaudiere() || $item->generateur()->pac_hybride())
            && $item->generateur()->energie()->is_combustible()
            && ModeCombustion::STANDARD === $item->generateur()->mode_combustion()
            && false === $item->generateur()->energie()->is_bois()
            && false === $item->generateur()->generateur_multi_batiment();
    }
}
