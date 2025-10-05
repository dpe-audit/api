<?php

namespace App\Engine\Rules\Ecs\Dimensionnement;

use App\Engine\Input\Ecs\SystemeInputRuleIterator;

final class DimensionnementSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Ratio de dimensionnement du système d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return 1 / count($this->item()->installation()->systemes()) * $this->item()->installation()->rdim();
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            rdim: $this->rdim(),
        ));
    }
}
