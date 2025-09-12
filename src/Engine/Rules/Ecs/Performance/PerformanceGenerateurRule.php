<?php

namespace App\Engine\Rules\Ecs\Performance;

use App\Engine\Input\Ecs\GenerateurInputRuleIterator;
use App\Engine\Table\EcsTableValeurRepository;

abstract class PerformanceGenerateurRule extends GenerateurInputRuleIterator
{
    public function __construct(
        protected EcsTableValeurRepository $repository
    ) {}

    /**
     * Coefficient de performance énergétique
     */
    public function cop(): ?float
    {
        return null;
    }

    /**
     * Rendement à pleine charge exprimée en %
     */
    public function rpn(): ?float
    {
        return null;
    }

    /**
     * Pertes à l'arrêt du générateur exprimée en W
     */
    public function qp0(): ?float
    {
        return null;
    }

    /**
     * Puissance de la veilleuse exprimée en W
     */
    public function pveilleuse(): ?float
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cop: $this->cop(),
            rpn: $this->rpn(),
            qp0: $this->qp0(),
            pveilleuse: $this->pveilleuse(),
        ));
    }
}
