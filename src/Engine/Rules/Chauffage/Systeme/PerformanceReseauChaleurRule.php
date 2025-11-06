<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Systeme;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;

final class PerformanceReseauChaleurRule extends PerformanceSystemeRule
{
    public static function supports(Systeme $entity): bool
    {
        return $entity->generateur()->type()?->is_reseau_chaleur()
            || $entity->generateur()->position()->generateur_multi_batiment;
    }

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', fn(): float => 0.97);
    }
}
