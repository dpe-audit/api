<?php

namespace App\Engine\Input\Chauffage;

use App\Domain\Chauffage\Systeme\{Configuration, Systeme};
use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, TypeDistribution};
use App\Domain\Chauffage\TypeChauffage;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Chauffage\{ConsommationAuxiliaireRule, ConsommationSystemeRule};
use App\Engine\Rules\Chauffage\Dimensionnement\DimensionnementSystemeRule;
use App\Engine\Rules\Chauffage\Perte\PerteSystemeRule;
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

    public function perte_rule(): PerteSystemeRule
    {
        return $this->requireIterator(PerteSystemeRule::class, $this);
    }

    public function consommation_auxiliaire_rule(): ConsommationAuxiliaireRule
    {
        return $this->requireIterator(ConsommationAuxiliaireRule::class, $this);
    }

    public function consommation_rule(): ConsommationSystemeRule
    {
        return $this->requireIterator(ConsommationSystemeRule::class, $this);
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

    public function pertes_generation(): float
    {
        return $this->perte_rule()->pertes_generation();
    }

    public function pertes_generation_recuperables(): float
    {
        return $this->perte_rule()->pertes_generation_recuperables();
    }

    public function cef_ch(): float
    {
        return $this->consommation_rule()->cef_ch();
    }

    public function cep_ch(): float
    {
        return $this->consommation_rule()->cep_ch();
    }

    public function eges_ch(): float
    {
        return $this->consommation_rule()->eges_ch();
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
