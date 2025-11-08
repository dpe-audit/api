<?php

namespace App\Engine\Rules\Ecs\Generateur;

use App\Engine\Rules\Ecs\PerformanceGenerateurRule;

final class PerformanceGenerateurPacRule extends PerformanceGenerateurRule
{
    public function supports(): bool
    {
        return $this->type()->is_pac() && false === $this->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function cop(): float
    {
        return $this->get("cop", function (): float {
            if ($this->cop_saisi()) {
                return $this->cop_saisi();
            }
            return $this->repository->cop(
                zone_climatique: $this->zone_climatique(),
                type_generateur: $this->type(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException("Valeurs forfaitaires COP non trouvées");
        });
    }
}
