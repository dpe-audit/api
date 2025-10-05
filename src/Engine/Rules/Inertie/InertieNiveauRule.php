<?php

namespace App\Engine\Rules\Inertie;

use App\Domain\Enveloppe\Inertie;
use App\Domain\Enveloppe\Paroi\Inertie as InertieParoi;
use App\Engine\Input\Enveloppe\NiveauInputRuleIterator;

final class InertieNiveauRule extends NiveauInputRuleIterator
{
    /**
     * Etat d'inertie du niveau
     */
    public function inertie(): Inertie
    {
        return $this->get('inertie', function () {
            $inerties = array_filter([
                $this->item()->inertie_paroi_verticale(),
                $this->item()->inertie_plancher_haut(),
                $this->item()->inertie_plancher_bas(),
            ], fn(InertieParoi $item) => $item === InertieParoi::LOURDE);

            return match (true) {
                count($inerties) === 0 => Inertie::LEGERE,
                count($inerties) === 1 => Inertie::MOYENNE,
                count($inerties) === 2 => Inertie::LOURDE,
                count($inerties) === 3 => Inertie::TRES_LOURDE,
            };
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule(
            $this->item()->entity->data()->with(inertie: $this->inertie())
        );
    }
}
