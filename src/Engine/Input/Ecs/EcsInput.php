<?php

namespace App\Engine\Input\Ecs;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Generateur\Generateur;
use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Ecs\BesoinEcsRule;
use App\Engine\Rules\Ecs\Perte\PerteEcsRule;
use App\Engine\Rules\Performance\{PerformanceAuxiliairesEcsRule, PerformanceEcsRule};

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

    public function pertes_rule(): PerteEcsRule
    {
        return $this->require(PerteEcsRule::class);
    }

    public function performance_rule(): PerformanceEcsRule
    {
        return $this->require(PerformanceEcsRule::class);
    }

    public function performance_auxiliaires_rule(): PerformanceAuxiliairesEcsRule
    {
        return $this->require(PerformanceAuxiliairesEcsRule::class);
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

    public function pertes(): float
    {
        return $this->pertes_rule()->pertes();
    }

    public function pertes_recuperables(?Mois $mois = null): float
    {
        return $mois ? $this->pertes_rule()->pertes_recuperables_j($mois) : $this->pertes_rule()->pertes_recuperables();
    }

    public function cef(): float
    {
        return $this->performance_rule()->cef();
    }

    public function cep(): float
    {
        return $this->performance_rule()->cep();
    }

    public function eges(): float
    {
        return $this->performance_rule()->eges();
    }

    public function cef_auxiliaires(): float
    {
        return $this->performance_auxiliaires_rule()->cef();
    }

    public function cep_auxiliaires(): float
    {
        return $this->performance_auxiliaires_rule()->cep();
    }

    public function eges_auxiliaires(): float
    {
        return $this->performance_auxiliaires_rule()->eges();
    }
}
