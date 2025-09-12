<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementCombustion;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Engine\Rules\Chauffage\Rendement\RendementSystemeRule;

abstract class RendementCombustionRule extends RendementSystemeRule
{
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
        return $rg_pcs * $this->item()->generateur()->energie()->to()->coefficient_conversion_pcs();
    }

    /**
     * Puissance fournie par le générateur au point de fonctionnement x (kW)
     */
    public function pmfou(TauxCharge $x): float
    {
        return $this->item()->generateur()->pn() * $this->tch_final($x) * $this->coeff_pond_final($x);
    }

    /**
     * Puissance consommée par le générateur au point de fonctionnement x (kW)
     */
    public function pmcons(TauxCharge $x)
    {
        $qpx = $this->qp($x);
        $pmfou = $this->pmfou($x);
        $p = $this->item()->generateur()->pn() * $this->tch_final($x);
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
     * @see https://github.com/action-21/reno-audit/discussions/44
     */
    public function tch_dim(TauxCharge $x): float
    {
        $priorite_cascade = $this->item()->generateur()->priorite_cascade();
        $tch = $this->tch($x);

        if (null === $priorite_cascade) {
            return $x === TauxCharge::TCH95 ? $tch : \min($tch / $this->cdim_ref(), 1);
        }
        return $tch;
    }

    /**
     * Taux de charge final au point de fonctionnement x (%)
     */
    public function tch_final(TauxCharge $x): float
    {
        $priorite_cascade = $this->item()->generateur()->priorite_cascade();
        $tch_dim = $this->tch_dim($x);

        if (null === $priorite_cascade) {
            return $tch_dim;
        }
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
        $priorite_cascade = $this->item()->generateur()->priorite_cascade();
        $coeff_pond = $this->coeff_pond($x);

        if (null === $priorite_cascade) {
            return $coeff_pond;
        }
        $tch_dim = $this->tch_dim($x);
        $prel = $this->prel();
        $ctch = $this->ctch($x, $prel);

        return $coeff_pond * ($ctch / $tch_dim);
    }

    /**
     * Coefficient de pondération final au point de fonctionnement x (%)
     */
    public function coeff_pond_final(TauxCharge $x): float
    {
        $priorite_cascade = $this->item()->generateur()->priorite_cascade();
        $coeff_pond_dim = $this->coeff_pond_dim(x: $x);

        if (null === $priorite_cascade) {
            return $coeff_pond_dim;
        }
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
        $gv = $this->data()->enveloppe->gv();
        $tbase = $this->data()->batiment->tbase();
        return (1000 * $this->pn_combustion()) / ($gv * ($tcons - $tbase));
    }

    /**
     * Contribution du générateur en cascade au taux de charge au point de fonctionnement x (%)
     * 
     * @param float $prel - Puissance relative du générateur en cascade
     */
    public function ctch(TauxCharge $x, float $prel): float
    {
        $priorite_cascade = $this->item()->generateur()->priorite_cascade();
        $tch_dim = $this->tch_dim(x: $x);

        if (0 === $priorite_cascade) {
            return $tch_dim * $prel;
        }
        if (1 === $priorite_cascade) {
            return \min($prel, $tch_dim);
        }
        if (2 === $priorite_cascade) {
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
        return $this->item()->generateur()->pn() /  $this->pn_cascade();
    }

    /**
     * Sommes des puissances nominales des générateurs en cascade
     * 
     * @see https://github.com/dpe-audit/schemas/discussions/23
     */
    private function pn_cascade(): float
    {
        $cascade = $this->item()->generateur()->cascade();
        $pn_cascade = 0;
        foreach ($this->collection() as $item) {
            if ($item->generateur()->cascade() !== $cascade) {
                continue;
            }
            $pn_cascade += $item->generateur()->pn();
        }
        return $pn_cascade;
    }

    /**
     * Sommes des puissances nominales des générateurs à combustion
     */
    private function pn_combustion(): float
    {
        $pn_combustion = 0;
        foreach ($this->collection() as $item) {
            if ($item->generateur()->cascade()) {
                continue;
            }
            $pn_combustion += $item->generateur()->pn();
        }
        return $pn_combustion;
    }

    protected function pn(): float
    {
        return $this->item()->generateur()->pn();
    }

    protected function rpn(): float
    {
        return $this->item()->generateur()->rpn();
    }

    protected function rpint(): float
    {
        return $this->item()->generateur()->rpint();
    }

    protected function qp0(): float
    {
        return $this->item()->generateur()->qp0() / 1000;
    }

    protected function pveilleuse(): float
    {
        return $this->item()->generateur()->pveilleuse() / 1000;
    }

    protected function tfonc30(): ?float
    {
        return $this->item()->generateur()->tfonc30();
    }

    protected function tfonc100(): ?float
    {
        return $this->item()->generateur()->tfonc100();
    }
}
