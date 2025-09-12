<?php

namespace App\Engine\Input\Refroidissement;

use App\Domain\Refroidissement\Systeme\Systeme;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Refroidissement\{ConsommationRefroidissementRule, DimensionnementSystemeRule};

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

    public function consommation_rule(): ConsommationRefroidissementRule
    {
        return $this->require(ConsommationRefroidissementRule::class);
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function cef(): float
    {
        return $this->consommation_rule()->cef();
    }

    public function cep(): float
    {
        return $this->consommation_rule()->cep();
    }

    public function eges(): float
    {
        return $this->consommation_rule()->eges();
    }
}
