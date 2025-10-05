<?php

namespace App\Engine\Input\Ecs;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Perte\PerteCollection;
use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Installation\Solaire\Usage;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Ecs\Dimensionnement\DimensionnementInstallationRule;
use App\Engine\Rules\Ecs\Rendement\RendementInstallationRule;
use App\Engine\Rules\Ecs\ConsommationInstallationRule;
use App\Engine\Rules\Ecs\Perte\PerteInstallationRule;

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

    /**
     * @return SystemeInput[]
     */
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

    public function pertes(): PerteCollection
    {
        /** @var PerteInstallationRule $rule */
        $rule = $this->requireIterator(PerteInstallationRule::class, $this);
        return $rule->pertes();
    }

    public function consommations(): ConsommationCollection
    {
        /** @var ConsommationInstallationRule $rule */
        $rule = $this->requireIterator(ConsommationInstallationRule::class, $this);
        return $rule->consommations();
    }
}
