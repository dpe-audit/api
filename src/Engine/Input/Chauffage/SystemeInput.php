<?php

namespace App\Engine\Input\Chauffage;

use App\Domain\Chauffage\Systeme\{Configuration, Systeme};
use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, TypeDistribution};
use App\Domain\Chauffage\TypeChauffage;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Chauffage\{ConsommationAuxiliaireRule, ConsommationChauffageRule};
use App\Engine\Rules\Chauffage\Dimensionnement\DimensionnementSystemeRule;
use App\Engine\Rules\Chauffage\Rendement\RendementSystemeRule;

final class SystemeInput extends Input
{
    private ?InstallationInput $installation = null;
    private ?GenerateurInput $generateur = null;
    private ?array $emetteurs = null;

    public function __construct(
        public readonly Engine $context,
        public readonly Systeme $entity,
    ) {}

    public function installation(): InstallationInput
    {
        return $this->installation ??= array_find(
            $this->context->data()->chauffage->installations,
            fn(InstallationInput $item) => $item->entity === $this->entity->installation()
        );
    }

    public function generateur(): GenerateurInput
    {
        return $this->generateur ??= array_find(
            $this->context->data()->chauffage->generateurs,
            fn(GenerateurInput $item) => $item->entity === $this->entity->generateur()
        );
    }

    /**
     * @return EmetteurInput[]
     */
    public function emetteurs(): array
    {
        return $this->emetteurs ??= array_find(
            $this->context->data()->chauffage->emetteurs,
            fn(EmetteurInput $item) => null !== $this->entity->emetteurs()->find($item->entity->id())
        );
    }

    public function type(): TypeChauffage
    {
        return $this->entity->type();
    }

    public function systeme_collectif(): bool
    {
        return $this->entity->generateur()->position()->generateur_collectif;
    }

    public function isolation_reseau(): IsolationReseau
    {
        return $this->entity->reseau()->isolation ?? IsolationReseau::NON_ISOLE;
    }

    public function type_distribution(): TypeDistribution
    {
        return $this->entity->reseau()->type_distribution;
    }

    public function niveaux_desservis(): int
    {
        return $this->entity->reseau()->niveaux_desservis;
    }

    // * Données calculées

    public function dimensionnement_rule(): DimensionnementSystemeRule
    {
        return $this->requireIterator(DimensionnementSystemeRule::class, $this);
    }

    public function rendement_rule(): RendementSystemeRule
    {
        return $this->requireIterator(RendementSystemeRule::class, $this);
    }

    public function consommation_auxiliaire_rule(): ConsommationAuxiliaireRule
    {
        return $this->requireIterator(ConsommationAuxiliaireRule::class, $this);
    }

    public function consommation_rule(): ConsommationChauffageRule
    {
        return $this->requireIterator(ConsommationChauffageRule::class, $this);
    }

    public function configuration(): Configuration
    {
        return $this->dimensionnement_rule()->configuration();
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function ich(): float
    {
        return $this->rendement_rule()->ich();
    }

    public function rd(): float
    {
        return $this->rendement_rule()->rd();
    }

    public function re(): float
    {
        return $this->rendement_rule()->re();
    }

    public function rg(): float
    {
        return $this->rendement_rule()->rg();
    }

    public function rr(): float
    {
        return $this->rendement_rule()->rr();
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

    public function cef_auxiliaire(): float
    {
        return $this->consommation_auxiliaire_rule()->cef();
    }

    public function cep_auxiliaire(): float
    {
        return $this->consommation_auxiliaire_rule()->cep();
    }

    public function eges_auxiliaire(): float
    {
        return $this->consommation_auxiliaire_rule()->eges();
    }
}
