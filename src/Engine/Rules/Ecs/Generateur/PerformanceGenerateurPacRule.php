<?php

namespace App\Engine\Rules\Ecs\Generateur;

use App\Domain\Ecs\Generateur\Generateur;
use App\Engine\Rules\Ecs\PerformanceGenerateurRule;

final class PerformanceGenerateurPacRule extends PerformanceGenerateurRule
{
    public static function supports(Generateur $entity): bool
    {
        return $entity->type()?->is_pac() && false === $entity->position()->generateur_multi_batiment;
    }

    // * Données d'entrée

    public function cop_saisi(): ?float
    {
        return $this->item()->signaletique()->cop;
    }

    // * Données de sortie

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
