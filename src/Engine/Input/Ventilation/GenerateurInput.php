<?php

namespace App\Engine\Input\Ventilation;

use App\Domain\Ventilation\Generateur\{Generateur, TypeGenerateur, TypeVmc};
use App\Engine\{Engine, Input};
use App\Engine\Rules\Ventilation\ConsommationAuxiliaireRule;
use App\Engine\Rules\Ventilation\DimensionnementGenerateurRule;

final class GenerateurInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Generateur $entity,
    ) {}

    /**
     * @return InstallationInput[]
     */
    public function installations(): array
    {
        return array_filter(
            $this->context->data()->ventilation->installations,
            fn(InstallationInput $item) => $item->entity->generateur()->id()->equals($this->entity->id()),
        );
    }

    public function type(): TypeGenerateur
    {
        return $this->entity->type();
    }

    public function type_vmc(): TypeVmc
    {
        return $this->entity->type_vmc() ?? TypeVmc::AUTOREGLABLE;
    }

    public function generateur_collectif(): bool
    {
        return $this->entity->generateur_collectif();
    }

    public function presence_echangeur_thermique(): bool
    {
        return $this->entity->presence_echangeur_thermique() ?? false;
    }

    public function annee_installation(): int
    {
        return $this->entity->annee_installation()
            ?? $this->context->data()->batiment->annee_construction();
    }

    // * Données calculées

    public function dimensionnement_rule(): DimensionnementGenerateurRule
    {
        return $this->requireIterator(DimensionnementGenerateurRule::class, $this);
    }

    public function consommation_auxiliaire_rule(): ConsommationAuxiliaireRule
    {
        return $this->requireIterator(ConsommationAuxiliaireRule::class, $this);
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function cef_aux(): float
    {
        return $this->consommation_auxiliaire_rule()->cef_aux();
    }

    public function cep_aux(): float
    {
        return $this->consommation_auxiliaire_rule()->cep_aux();
    }

    public function eges_aux(): float
    {
        return $this->consommation_auxiliaire_rule()->eges_aux();
    }
}
