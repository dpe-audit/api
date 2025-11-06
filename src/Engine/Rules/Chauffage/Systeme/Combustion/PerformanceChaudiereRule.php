<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Domain\Chauffage\Systeme\Systeme;
use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;
use App\Engine\Rules\Chauffage\Systeme\PerformanceCombustionRule;

abstract class PerformanceChaudiereRule extends PerformanceCombustionRule
{
    public static function supports(Systeme $entity): bool
    {
        $match = $entity->generateur()->type()?->is_chaudiere();
        $match = $match || ($entity->generateur()->type()?->is_pac() && null !== $entity->generateur()->bienergie());
        $match = $match || null === $entity->generateur()->type();
        $match = $match && false === $entity->generateur()->position()->generateur_multi_batiment;
        return $match;
    }

    // * Données d'entrée

    public function regulation(): bool
    {
        return $this->regulation_centrale() || $this->regulation_terminale();
    }

    // * Données intermédiaires

    public function scop(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->scop();
    }

    // * Données de sortie

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
