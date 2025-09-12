<?php

namespace App\Engine\Input\Ecs;

use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Installation\Solaire\Usage;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Ecs\Dimensionnement\DimensionnementInstallationRule;
use App\Engine\Rules\Ecs\Rendement\RendementInstallationRule;

final class InstallationInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Installation $entity,
    ) {}

    public function generateurs(): array
    {
        return array_filter(
            $this->context->data()->ecs->generateurs,
            fn(GenerateurInput $item) => null !== $item->entity->installations()->find($this->entity->id())
        );
    }

    public function systemes(): array
    {
        return array_filter(
            $this->context->data()->ecs->systemes,
            fn(SystemeInput $item) => $item->entity->installation() === $this->entity
        );
    }

    public function surface(): float
    {
        return $this->entity->surface();
    }

    public function solaire_thermique(): bool
    {
        return $this->entity->solaire_thermique() !== null;
    }

    public function usage_solaire_thermique(): ?Usage
    {
        return $this->entity->solaire_thermique()?->usage;
    }

    public function annee_installation_solaire_thermique(): ?int
    {
        return $this->entity->solaire_thermique()?->annee_installation
            ?? $this->context->data()->batiment->annee_construction();
    }

    public function fecs_saisi(): ?float
    {
        return $this->entity->solaire_thermique()?->fecs;
    }

    // * Données calculées

    public function rdim(): float
    {
        /** @var DimensionnementInstallationRule $rule */
        $rule = $this->requireIterator(DimensionnementInstallationRule::class, $this);
        return $rule->rdim();
    }

    public function fecs(): float
    {
        /** @var RendementInstallationRule $rule */
        $rule = $this->requireIterator(RendementInstallationRule::class, $this);
        return $rule->fecs();
    }
}
