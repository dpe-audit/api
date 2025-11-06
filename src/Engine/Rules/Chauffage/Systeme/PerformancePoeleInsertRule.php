<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Systeme;
use App\Engine\Rules\Chauffage\PerformanceSystemeRule;

final class PerformancePoeleInsertRule extends PerformanceSystemeRule
{
    public static function supports(Systeme $entity): bool
    {
        return $entity->generateur()->type()?->is_poele_insert()
            && false === $entity->generateur()->position()->generateur_multi_batiment;
    }

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return $this->repository->rg(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                label_generateur: $this->label_generateur(),
                annee_installation_generateur: $this->annee_installation_generateur(),
            ) ?? throw new \DomainException('Valeur forfaitaire Rg non trouvée');
        });
    }
}
