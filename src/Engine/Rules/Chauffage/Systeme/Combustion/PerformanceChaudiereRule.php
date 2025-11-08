<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Engine\Rules\Chauffage\Systeme\PerformanceCombustionRule;

abstract class PerformanceChaudiereRule extends PerformanceCombustionRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_chaudiere()
            || ($this->type_generateur()->is_pac() && null !== $this->bienergie_generateur())
            && $this->energie_generateur()->is_combustible()
            && false === $this->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            if ($this->type_generateur()->is_pac()) {
                $scop = $this->scop();
                $taux_couverture_partie_pac = $this->zone_climatique()->taux_couverture_pac();
                $taux_couverture_partie_chaudiere = 1 - $taux_couverture_partie_pac;
                $rg = $this->rg_combustion() * $taux_couverture_partie_chaudiere;
                $rg += $scop * $taux_couverture_partie_pac;
                return $rg;
            }
            return $this->rg_combustion();
        });
    }

    /**
     * Pertes de charge à 100% de puissance
     */
    public function qp100(): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn();
        $tfonc = $this->tfonc100();

        $qp = 100 - ($rpn + 0.1 * (70 - $tfonc));
        $qp /= $rpn + 0.1 * (70 - $tfonc);
        $qp *= $pn;

        return $qp;
    }
}
