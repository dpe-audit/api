<?php

namespace App\Engine\Input\Ecs;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Systeme\Reseau\{BouclageReseau, IsolationReseau};
use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Ecs\{ConsommationAuxiliaireRule, ConsommationEcsRule};
use App\Engine\Rules\Ecs\Dimensionnement\DimensionnementSystemeRule;
use App\Engine\Rules\Ecs\Perte\PerteSystemeRule;
use App\Engine\Rules\Ecs\Rendement\RendementSystemeRule;

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
            $this->context->data()->ecs->generateurs,
            fn(GenerateurInput $item) => $item->entity === $this->entity->generateur()
        );
    }

    public function installation(): InstallationInput
    {
        return $this->installation ??= array_find(
            $this->context->data()->ecs->installations,
            fn(InstallationInput $item) => $item->entity === $this->entity->installation()
        );
    }

    public function volume_stockage(): float
    {
        return $this->entity->stockage()->volume ?? 50;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->entity->stockage()->position_volume_chauffe ?? false;
    }

    public function bouclage_reseau(): BouclageReseau
    {
        return $this->entity->reseau()->bouclage ?? BouclageReseau::RESEAU_BOUCLE;
    }

    public function alimentation_contigue(): bool
    {
        return $this->entity->reseau()->alimentation_contigue;
    }

    public function niveaux_desservis(): int
    {
        return $this->entity->reseau()->niveaux_desservis;
    }

    public function isolation_reseau(): IsolationReseau
    {
        return $this->entity->reseau()->isolation ?? IsolationReseau::NON_ISOLE;
    }

    // * Données calculées

    public function dimensionnement_rule(): DimensionnementSystemeRule
    {
        return $this->requireIterator(DimensionnementSystemeRule::class, $this);
    }

    public function perte_rule(): PerteSystemeRule
    {
        return $this->requireIterator(PerteSystemeRule::class, $this);
    }

    public function rendement_rule(): RendementSystemeRule
    {
        return $this->requireIterator(RendementSystemeRule::class, $this);
    }

    public function consommation_rule(): ConsommationEcsRule
    {
        return $this->requireIterator(ConsommationEcsRule::class, $this);
    }

    public function consommation_auxiliaire_rule(): ConsommationAuxiliaireRule
    {
        return $this->requireIterator(ConsommationAuxiliaireRule::class, $this);
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function iecs(): float
    {
        return $this->rendement_rule()->iecs();
    }

    public function rd(): float
    {
        return $this->rendement_rule()->rd();
    }

    public function rs(): float
    {
        return $this->rendement_rule()->rs();
    }

    public function rgs(): float
    {
        return $this->rendement_rule()->rgs();
    }

    public function rg(): float
    {
        return $this->rendement_rule()->rg();
    }

    public function pertes_distribution(?Mois $mois = null): float
    {
        return $mois ? $this->perte_rule()->pertes_distribution_j($mois) : $this->perte_rule()->pertes_distribution();
    }

    public function pertes_distribution_recuperables(?Mois $mois = null): float
    {
        return $mois ? $this->perte_rule()->pertes_distribution_recuperables_j($mois) : $this->perte_rule()->pertes_distribution_recuperables();
    }

    public function pertes_stockage(): float
    {
        return $this->perte_rule()->pertes_stockage();
    }

    public function pertes_stockage_recuperables(Mois $mois): float
    {
        return $this->perte_rule()->pertes_stockage_recuperables_j($mois);
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
