<?php

namespace App\Engine\Input\Refroidissement;

use App\Domain\Refroidissement\Systeme\Systeme;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Refroidissement\{ConsommationAuxiliaireRule, ConsommationSystemeRule, DimensionnementSystemeRule};

final class SystemeInput extends Input
{
    private ?GenerateurInput $generateur = null;
    private ?InstallationInput $installation = null;

    public function __construct(
        public readonly Engine $context,
        public readonly Systeme $entity,
    ) {}

    public function generateur(): GenerateurInput
    {
        return $this->generateur ??= array_find(
            $this->context->data()->refroidissement->generateurs,
            fn(GenerateurInput $item) => $item->entity === $this->entity->generateur()
        );
    }

    public function installation(): InstallationInput
    {
        return $this->installation ??= array_find(
            $this->context->data()->refroidissement->installations,
            fn(InstallationInput $item) => $item->entity === $this->entity->installation()
        );
    }

    public function systemes(): array
    {
        return $this->installation()->systemes();
    }

    // * Données calculées

    public function dimensionnement_rule(): DimensionnementSystemeRule
    {
        return $this->require(DimensionnementSystemeRule::class);
    }

    public function consommation_refroidissement_rule(): ConsommationSystemeRule
    {
        return $this->require(ConsommationSystemeRule::class);
    }

    public function consommation_auxiliaire_rule(): ConsommationAuxiliaireRule
    {
        return $this->require(ConsommationSystemeRule::class);
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function cef_fr(): float
    {
        return $this->consommation_refroidissement_rule()->cef_fr();
    }

    public function cep_fr(): float
    {
        return $this->consommation_refroidissement_rule()->cep_fr();
    }

    public function eges_fr(): float
    {
        return $this->consommation_refroidissement_rule()->eges_fr();
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
