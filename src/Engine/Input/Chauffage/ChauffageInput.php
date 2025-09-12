<?php

namespace App\Engine\Input\Chauffage;

use App\Domain\Chauffage\Emetteur\Emetteur;
use App\Domain\Chauffage\Generateur\Generateur;
use App\Domain\Chauffage\Installation\Installation;
use App\Domain\Chauffage\Systeme\Systeme;
use App\Domain\Common\Enum\Mois;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Chauffage\BesoinChauffageRule;
use App\Engine\Rules\Chauffage\Perte\PerteChauffageRule;
use App\Engine\Rules\Performance\{PerformanceAuxiliairesChauffageRule, PerformanceChauffageRule};

final class ChauffageInput extends Input
{
    /** @var InstallationInput[] */
    public readonly array $installations;
    /** @var GenerateurInput[] */
    public readonly array $generateurs;
    /** @var EmetteurInput[] */
    public readonly array $emetteurs;
    /** @var SystemeInput[] */
    public readonly array $systemes;

    public function __construct(public readonly Engine $context)
    {
        $this->installations = $context->ressource()->chauffage()->installations()
            ->map(fn(Installation $item) => new InstallationInput($context, $item))
            ->values();
        $this->generateurs = $context->ressource()->chauffage()->generateurs()
            ->map(fn(Generateur $item) => new GenerateurInput($context, $item))
            ->values();
        $this->emetteurs = $context->ressource()->chauffage()->emetteurs()
            ->map(fn(Emetteur $item) => new EmetteurInput($context, $item))
            ->values();
        $this->systemes = $context->ressource()->chauffage()->systemes()
            ->map(fn(Systeme $item) => new SystemeInput($context, $item))
            ->values();
    }

    // * Données calculées

    public function perte_rule(): PerteChauffageRule
    {
        return $this->require(PerteChauffageRule::class);
    }

    public function besoin_rule(): BesoinChauffageRule
    {
        return $this->require(BesoinChauffageRule::class);
    }

    public function performance_auxiliaires_rule(): PerformanceAuxiliairesChauffageRule
    {
        return $this->require(PerformanceAuxiliairesChauffageRule::class);
    }

    public function performance_rule(): PerformanceChauffageRule
    {
        return $this->require(PerformanceChauffageRule::class);
    }

    public function pertes(?Mois $mois = null): float
    {
        $rule = $this->perte_rule();
        return $mois ? $rule->pertes_j($mois) : $rule->pertes();
    }

    public function pertes_recuperables(?Mois $mois = null): float
    {
        $rule = $this->perte_rule();
        return $mois ? $rule->pertes_recuperables_j($mois) : $rule->pertes_recuperables();
    }

    public function bch_hp(?Mois $mois = null): float
    {
        return $mois ? $this->besoin_rule()->bch_hp_j($mois) : $this->besoin_rule()->bch_hp();
    }

    public function bch(?Mois $mois = null): float
    {
        return $mois ? $this->besoin_rule()->bch($mois) : $this->besoin_rule()->bch();
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
