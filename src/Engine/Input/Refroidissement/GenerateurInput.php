<?php

namespace App\Engine\Input\Refroidissement;

use App\Domain\Refroidissement\Generateur\EnergieGenerateur;
use App\Domain\Refroidissement\Generateur\Generateur;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Refroidissement\PerformanceGenerateurRule;

final class GenerateurInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Generateur $entity,
    ) {}

    public function seer_saisi(): ?float
    {
        return $this->entity->seer();
    }

    public function annee_installation(): int
    {
        return current(array_filter([
            $this->entity->annee_installation(),
            $this->context->data()->batiment->annee_construction()
        ]));
    }

    public function energie(): EnergieGenerateur
    {
        return $this->entity->energie();
    }

    public function contenu_co2_reseau_froid(): ?float
    {
        return $this->entity->reseau_froid()?->contenu_co2();
    }

    // * Données calculées

    public function eer(): float
    {
        /** @var PerformanceGenerateurRule $rule */
        $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this);
        return $rule->eer();
    }
}
