<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Systeme;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;

final class PerformancePacRule extends PerformanceSystemeRule
{
    public static function supports(Systeme $entity): bool
    {
        return $entity->generateur()->type()?->is_pac()
            && null === $entity->generateur()->bienergie()
            && false === $entity->generateur()->position()->generateur_multi_batiment;
    }

    public function scop_saisi(): ?float
    {
        return $this->item()->generateur()->signaletique()->scop;
    }

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return $this->scop_saisi() ?? throw new \DomainException('SCOP non calculée');
        });
    }
}
