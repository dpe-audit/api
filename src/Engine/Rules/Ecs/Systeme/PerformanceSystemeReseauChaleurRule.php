<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Reseau\IsolationReseau;
use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\Rules\Ecs\PerformanceSystemeRule;

final class PerformanceSystemeReseauChaleurRule extends PerformanceSystemeRule
{
    public static function supports(Systeme $entity): bool
    {
        return $entity->generateur()->type()?->is_reseau_chaleur()
            || $entity->generateur()->position()->generateur_multi_batiment;
    }

    // * Données d'entrée

    public function isolation_reseau(): IsolationReseau
    {
        return $this->item()->reseau()->isolation ?? IsolationReseau::NON_ISOLE;
    }

    // * Données de sortie

    /**
     * @inheritDoc
     */
    public function rgs(): float
    {
        return match ($this->isolation_reseau()) {
            IsolationReseau::ISOLE => 0.9,
            IsolationReseau::NON_ISOLE => 0.75,
            default => 0.75,
        };
    }
}
