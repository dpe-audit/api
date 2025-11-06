<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Systeme;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;

final class PerformanceGenerateurEffetJouleRule extends PerformanceSystemeRule
{
    public static function supports(Systeme $entity): bool
    {
        return $entity->generateur()->energie()?->is_electricite()
            && false === $entity->generateur()->type()?->is_pac()
            && false === $entity->generateur()->position()->generateur_multi_batiment;
    }

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return $this->type_generateur()->is_chaudiere() ? 0.97 : 1;
        });
    }
}
