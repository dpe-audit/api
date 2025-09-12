<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\Inertie;
use App\Domain\Enveloppe\Niveau\{InertieParoi, Niveau};
use App\Engine\{Engine, Input};
use App\Engine\Rules\Inertie\InertieNiveauRule;

final class NiveauInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Niveau $entity,
    ) {}

    public function surface(): float
    {
        return $this->entity->surface();
    }

    public function inertie_paroi_verticale(): InertieParoi
    {
        return $this->entity->inertie_paroi_verticale();
    }

    public function inertie_plancher_haut(): InertieParoi
    {
        return $this->entity->inertie_plancher_haut();
    }

    public function inertie_plancher_bas(): InertieParoi
    {
        return $this->entity->inertie_plancher_bas();
    }

    // * Données calculées

    public function inertie(): Inertie
    {
        /** @var InertieNiveauRule $rule */
        $rule = $this->requireIterator(InertieNiveauRule::class, $this);
        return $rule->inertie();
    }
}
