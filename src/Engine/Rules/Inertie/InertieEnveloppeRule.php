<?php

namespace App\Engine\Rules\Inertie;

use App\Domain\Enveloppe\Inertie;
use App\Engine\Input\Enveloppe\NiveauInput;
use App\Engine\Rule;

final class InertieEnveloppeRule extends Rule
{
    /**
     * Etat d'inertie de l'enveloppe
     */
    public function inertie(): Inertie
    {
        return $this->get('inertie', function (): Inertie {
            $inerties = [];
            $inerties[Inertie::TRES_LOURDE->value] = $this->surface_inertie(Inertie::TRES_LOURDE);
            $inerties[Inertie::LOURDE->value] = $this->surface_inertie(Inertie::LOURDE);
            $inerties[Inertie::MOYENNE->value] = $this->surface_inertie(Inertie::MOYENNE);
            $inerties[Inertie::LEGERE->value] = $this->surface_inertie(Inertie::LEGERE);
            $inerties = max($inerties);

            if (count($inerties) === 1) {
                return Inertie::from(current(array_keys($inerties)));
            }
            if (count($inerties) === 1) {
                return Inertie::from(current(array_keys($inerties)));
            }
            return in_array(Inertie::LEGERE, $inerties) ? Inertie::MOYENNE : Inertie::LOURDE;
        });
    }

    private function surface_inertie(Inertie $inertie): float
    {
        $collection = array_filter(
            $this->data()->enveloppe->niveaux,
            fn(NiveauInput $item) => $item->inertie() === $inertie
        );
        return array_reduce($collection, fn(float $carry, NiveauInput $item) => $carry += $item->surface(), 0);
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->enveloppe()->calcule($this->ressource()->enveloppe()->data()->with(
            inertie: $this->inertie(),
        ));
    }
}
