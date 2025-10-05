<?php

namespace App\Engine\Input\Ecs;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Perte\PerteCollection;
use App\Domain\Ecs\Generateur\Generateur;
use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Ecs\{BesoinEcsRule, ConsommationEcsRule};
use App\Engine\Rules\Ecs\Perte\PerteEcsRule;

final class EcsInput extends Input
{
    /** @var InstallationInput[] */
    public readonly array $installations;
    /** @var GenerateurInput[] */
    public readonly array $generateurs;
    /** @var SystemeInput[] */
    public readonly array $systemes;

    public function __construct(public readonly Engine $context)
    {
        $this->installations = $context->ressource()->ecs()->installations()
            ->map(fn(Installation $item) => new InstallationInput($context, $item))
            ->values();
        $this->generateurs = $context->ressource()->ecs()->generateurs()
            ->map(fn(Generateur $item) => new GenerateurInput($context, $item))
            ->values();
        $this->systemes = $context->ressource()->ecs()->systemes()
            ->map(fn(Systeme $item) => new SystemeInput($context, $item))
            ->values();
    }

    // * Données calculées

    public function besoin_rule(): BesoinEcsRule
    {
        return $this->require(BesoinEcsRule::class);
    }

    public function consommation_rule(): ConsommationEcsRule
    {
        return $this->require(ConsommationEcsRule::class);
    }

    public function becs(?Mois $mois = null): float
    {
        return $mois ? $this->besoin_rule()->becs_j($mois) : $this->besoin_rule()->becs();
    }

    public function nadeq(): float
    {
        return $this->besoin_rule()->nadeq();
    }

    public function nmax(): float
    {
        return $this->besoin_rule()->nmax();
    }

    public function pertes(): PerteCollection
    {
        /** @var PerteEcsRule $rule */
        $rule = $this->require(PerteEcsRule::class);
        return $rule->pertes();
    }

    public function pertes_recuperables(?Mois $mois = null): float
    {
        /** @var PerteEcsRule $rule */
        $rule = $this->require(PerteEcsRule::class);
        return $mois ? $rule->pertes_recuperables_j($mois) : $rule->pertes_recuperables();
    }

    public function consommations(): ConsommationCollection
    {
        return $this->consommation_rule()->consommations();
    }

    public function cef_ecs(): float
    {
        return $this->consommation_rule()->cef_ecs();
    }

    public function cep_ecs(): float
    {
        return $this->consommation_rule()->cep_ecs();
    }

    public function eges_ecs(): float
    {
        return $this->consommation_rule()->eges_ecs();
    }

    public function cef_aux(): float
    {
        return $this->consommation_rule()->cef_aux();
    }

    public function cep_aux(): float
    {
        return $this->consommation_rule()->cep_aux();
    }

    public function eges_aux(): float
    {
        return $this->consommation_rule()->eges_aux();
    }
}
