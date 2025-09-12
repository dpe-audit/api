<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementCombustion;

use App\Engine\Rules\Chauffage\Rendement\RendementCombustion\RendementCombustionRule;

abstract class RendementChaudiereRule extends RendementCombustionRule
{
    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            if ($this->item()->generateur()->type()->is_pac()) {
                $scop = $this->item()->generateur()->scop();
                $taux_couverture_partie_pac = $this->data()->batiment->zone_climatique()->taux_couverture_pac();
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

    protected function regulation(): bool
    {
        return $this->item()->installation()->regulation_centrale()->presence_regulation
            || $this->item()->installation()->regulation_terminale()->presence_regulation;
    }
}
