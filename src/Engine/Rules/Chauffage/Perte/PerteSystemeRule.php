<?php

namespace App\Engine\Rules\Chauffage\Perte;

use App\Domain\Chauffage\Systeme\Pertes;
use App\Engine\Input\Chauffage\SystemeInputRuleIterator;

final class PerteSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Pertes de génération exprimées en Wh
     */
    public function pertes_generation(): float
    {
        return $this->get('pertes_generation', function (): float {
            return $this->item()->generateur()->pertes_generation() * $this->item()->rdim();
        });
    }

    /**
     * Pertes de génération récupérables exprimées en Wh
     */
    public function pertes_generation_recuperables(): float
    {
        return $this->get('pertes_generation_recuperables', function (): float {
            return $this->item()->generateur()->pertes_generation_recuperables() * $this->item()->rdim();
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            pertes: Pertes::create(
                pertes_generation: $this->pertes_generation(),
                pertes_generation_recuperables: $this->pertes_generation_recuperables(),
            )
        ));
    }
}
