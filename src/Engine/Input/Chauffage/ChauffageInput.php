<?php

namespace App\Engine\Input\Chauffage;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Emetteur\Emetteur;
use App\Domain\Chauffage\Generateur\Generateur;
use App\Domain\Chauffage\Installation\Installation;
use App\Domain\Chauffage\Systeme\Systeme;
use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Perte\PerteCollection;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Chauffage\{BesoinChauffageRule, ConsommationChauffageRule};
use App\Engine\Rules\Chauffage\Perte\PerteChauffageRule;

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

    public function __construct(public readonly Chauffage $entity, public readonly Engine $context)
    {
        $this->installations = $entity->installations()
            ->map(fn(Installation $item) => new InstallationInput($context, $item))
            ->values();
        $this->generateurs = $entity->generateurs()
            ->map(fn(Generateur $item) => new GenerateurInput($context, $item))
            ->values();
        $this->emetteurs = $entity->emetteurs()
            ->map(fn(Emetteur $item) => new EmetteurInput($context, $item))
            ->values();
        $this->systemes = $entity->systemes()
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

    public function consommation_rule(): ConsommationChauffageRule
    {
        return $this->require(ConsommationChauffageRule::class);
    }

    public function bch_hp(?Mois $mois = null): float
    {
        return $mois ? $this->besoin_rule()->bch_hp_j($mois) : $this->besoin_rule()->bch_hp();
    }

    public function bch(?Mois $mois = null): float
    {
        return $mois ? $this->besoin_rule()->bch($mois) : $this->besoin_rule()->bch();
    }

    public function pertes_recuperables(?Mois $mois = null): float
    {
        $rule = $this->perte_rule();
        return $mois ? $rule->pertes_recuperables_j($mois) : $rule->pertes_recuperables();
    }

    public function pertes(): PerteCollection
    {
        return $this->perte_rule()->pertes();
    }

    public function consommations(): ConsommationCollection
    {
        return $this->consommation_rule()->consommations();
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
