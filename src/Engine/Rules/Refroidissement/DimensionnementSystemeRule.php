<?php

namespace App\Engine\Rules\Refroidissement;

use App\Engine\Input\Refroidissement\SystemeInputRuleIterator;

final class DimensionnementSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Ratio de dimensionnement du système de refroidissement
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
            rdim: $this->rdim()
        ));
    }
}
