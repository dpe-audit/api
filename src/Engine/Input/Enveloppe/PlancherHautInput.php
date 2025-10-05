<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\Paroi\Isolation\{EtatIsolation, TypeIsolation};
use App\Domain\Enveloppe\Paroi\{Mitoyennete, TypeParoi};
use App\Domain\Enveloppe\PlancherHaut\{PlancherHaut, Configuration, TypePlancherHaut};
use App\Engine\Engine;
use App\Engine\Rules\Deperdition\DeperditionPlancherHautRule;

final class PlancherHautInput extends ParoiInput
{
    public function __construct(
        public readonly Engine $context,
        public readonly PlancherHaut $entity,
    ) {}

    public function type_paroi(): TypeParoi
    {
        return $this->entity->type_paroi();
    }

    public function type_structure(): ?TypePlancherHaut
    {
        return $this->entity->type_structure();
    }

    public function configuration(): Configuration
    {
        return $this->entity->position()->local_non_chauffe
            ? Configuration::TERRASSE
            : $this->entity->configuration();
    }

    public function surface(): float
    {
        return $this->entity->position()->surface;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->entity->position()->mitoyennete;
    }

    public function annee_construction(): int
    {
        return current(array_filter([
            $this->entity->annee_renovation(),
            $this->entity->annee_construction(),
            $this->context->data()->batiment->annee_construction(),
        ]));
    }

    public function annee_construction_isolation(): int
    {
        if ($this->annee_isolation()) {
            return $this->annee_isolation();
        }
        if ($this->etat_isolation() === EtatIsolation::ISOLE) {
            return $this->annee_construction() <= 1974 ? 1976 : $this->annee_construction();
        }
        return $this->annee_construction();
    }

    public function isolation(): bool
    {
        return $this->entity->isolation()->etat?->toBoolean() ?? $this->annee_construction() < 1975;
    }

    public function etat_isolation(): ?EtatIsolation
    {
        return $this->entity->isolation()->etat;
    }

    public function type_isolation(): ?TypeIsolation
    {
        return $this->entity->isolation()->type;
    }

    public function annee_isolation(): ?int
    {
        return $this->entity->isolation()->annee_installation;
    }

    public function epaisseur_isolation(): ?float
    {
        return $this->entity->isolation()->epaisseur;
    }

    public function resistance_isolation(): ?float
    {
        return $this->entity->isolation()->resistance_thermique;
    }

    public function u_saisi(): ?float
    {
        return $this->entity->u();
    }

    public function u0_saisi(): ?float
    {
        return $this->entity->u0();
    }

    public function local_non_chauffe(): ?LncInput
    {
        return array_find(
            $this->context->data()->enveloppe->locaux_non_chauffes,
            fn(LncInput $item) => $item->entity === $this->entity->position()->local_non_chauffe
        );
    }

    public function sdep(): float
    {
        /** @var DeperditionPlancherHautRule $rule */
        $rule = $this->requireIterator(DeperditionPlancherHautRule::class, $this);
        return $rule->sdep();
    }

    public function dp(): float
    {
        /** @var DeperditionPlancherHautRule $rule */
        $rule = $this->requireIterator(DeperditionPlancherHautRule::class, $this);
        return $rule->dp();
    }
}
