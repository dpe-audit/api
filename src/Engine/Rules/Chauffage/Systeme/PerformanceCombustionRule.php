<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;
use App\Engine\Rules\Chauffage\Systeme\Combustion\TauxCharge;

abstract class PerformanceCombustionRule extends PerformanceSystemeRule
{
    /**
     * Sommes des puissances nominales des générateurs en cascade
     * 
     * @see https://github.com/dpe-audit/schemas/discussions/23
     */
    private function pn_cascade(): float
    {
        $pn = 0;
        foreach ($this->collection() as $entity) {
            $rule = $this->requireIterator(self::class, $entity);
            if (null === $rule->cascade()) {
                continue;
            }
            $pn += $rule->pn();
        }
        return $pn;
    }

    /**
     * Sommes des puissances nominales des générateurs à combustion
     */
    private function pn_combustion(): float
    {
        $pn = 0;
        foreach ($this->collection() as $entity) {
            $rule = $this->requireIterator(self::class, $entity);
            if (null !== $rule->cascade()) {
                continue;
            }
            $pn += $rule->pn();
        }
        return $pn;
    }

    // * Données de sortie

    /**
     * Pertes au point de fonctionnement x (kW)
     */
    abstract public function qp(TauxCharge $x): float;

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', fn(): float => $this->rg_combustion());
    }

    /**
     * Rendement de génération par combustion (PCI)
     */
    public function rg_combustion(): float
    {
        $pmfou = 0;
        $pmcons = 0;

        foreach (TauxCharge::cases() as $x) {
            $pmfou += $this->pmfou($x);
            $pmcons += $this->pmcons($x);
        }
        $rg_pcs = $pmfou / ($pmcons + 0.45 * $this->qp0() + $this->pveilleuse());
        return $rg_pcs * $this->energie_generateur()->to()->coefficient_conversion_pcs();
    }

    /**
     * Puissance fournie par le générateur au point de fonctionnement x (kW)
     */
    public function pmfou(TauxCharge $x): float
    {
        return $this->pn() * $this->tch_final($x) * $this->coeff_pond_final($x);
    }

    /**
     * Puissance consommée par le générateur au point de fonctionnement x (kW)
     */
    public function pmcons(TauxCharge $x)
    {
        $qpx = $this->qp($x);
        $pmfou = $this->pmfou($x);
        $p = $this->pn() * $this->tch_final($x);
        return $pmfou * (($p + $qpx) / $p);
    }

    /**
     * Taux de charge au point de fonctionnement x
     */
    public function tch(TauxCharge $x): float
    {
        return $x->taux_charge();
    }

    /**
     * Taux de charge intermédiaire au point de fonctionnement x (%)
     * 
     * TODO: Vérifier la méthode applicable aux générateurs en cascade
     * 
     * @see https://github.com/action-21/reno-audit/discussions/44
     */
    public function tch_dim(TauxCharge $x): float
    {
        $tch = $this->tch($x);

        // Absence de cascade
        if (null === $this->cascade()) {
            return $x === TauxCharge::TCH95 ? $tch : \min($tch / $this->cdim_ref(), 1);
        }
        // Système en cascade
        return $tch;
    }

    /**
     * Taux de charge final au point de fonctionnement x (%)
     */
    public function tch_final(TauxCharge $x): float
    {
        $tch_dim = $this->tch_dim($x);

        // Absence de cascade
        if (null === $this->cascade()) {
            return $tch_dim;
        }
        // Système en cascade
        $prel = $this->prel();
        $ctch = $this->ctch($x, $prel);
        return \min($ctch / $prel, 1);
    }

    /**
     * Coefficient de pondération au point de fonctionnement x
     */
    public function coeff_pond(TauxCharge $x): float
    {
        return $x->coefficient_ponderation();
    }

    /**
     * Coefficient de pondération intermédiaire au point de fonctionnement x
     * 
     * TODO: Vérifier la méthode applicable aux générateurs en cascade
     * @see https://github.com/action-21/reno-audit/discussions/44
     */
    public function coeff_pond_dim(TauxCharge $x): float
    {
        return $this->coeff_pond($x);
    }

    /**
     * Coefficient de pondération final au point de fonctionnement x (%)
     */
    public function coeff_pond_final(TauxCharge $x): float
    {
        $coeff_pond_dim = $this->coeff_pond_dim(x: $x);

        // Absence de cascade
        if (null === $this->cascade()) {
            return $coeff_pond_dim;
        }
        // Système en cascade
        return $coeff_pond_dim / \array_reduce(
            TauxCharge::cases(),
            fn(float $carry, TauxCharge $item): float => $carry += $this->coeff_pond_dim($item),
            0
        );
    }

    /**
     * Coefficient de pondération permettant de prendre en compte les charges partielles
     */
    public function cdim_ref(): float
    {
        $tcons = $this->scenario() === ScenarioUsage::CONVENTIONNEL ? 19 : 21;
        $gv = $this->gv();
        $tbase = $this->tbase();
        return (1000 * $this->pn_combustion()) / ($gv * ($tcons - $tbase));
    }

    /**
     * Contribution du générateur en cascade au taux de charge au point de fonctionnement x (%)
     * 
     * @param float $prel - Puissance relative du générateur en cascade
     */
    public function ctch(TauxCharge $x, float $prel): float
    {
        $cascade = $this->cascade();
        $tch_dim = $this->tch_dim(x: $x);

        // Cascade sans priorité
        if (0 === $cascade) {
            return $tch_dim * $prel;
        }
        // Système en cascade prioritaire
        if (1 === $cascade) {
            return \min($prel, $tch_dim);
        }
        // Système en cascade secondaire
        if (2 === $cascade) {
            $prel_1 = 1 - $prel;
            $ctch_1 = \min($prel_1, $tch_dim);
            return \min($prel, $tch_dim - $ctch_1);
        }
        return 0;
    }

    /**
     * Puissance relative du générateur en cascade
     */
    public function prel(): float
    {
        return $this->pn() /  $this->pn_cascade();
    }
}
