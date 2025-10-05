<?php

namespace App\Engine\Input\Refroidissement;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\Mois;
use App\Domain\Refroidissement\Generateur\Generateur;
use App\Domain\Refroidissement\Installation\Installation;
use App\Domain\Refroidissement\Systeme\Systeme;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Refroidissement\{BesoinRefroidissementRule, ConsommationRefroidissementRule};

final class RefroidissementInput extends Input
{
    /** @var InstallationInput[] */
    public readonly array $installations;
    /** @var GenerateurInput[] */
    public readonly array $generateurs;
    /** @var SystemeInput[] */
    public readonly array $systemes;

    public function __construct(public readonly Engine $context)
    {
        $this->installations = $context->ressource()->refroidissement()->installations()
            ->map(fn(Installation $item) => new InstallationInput($context, $item))
            ->values();
        $this->generateurs = $context->ressource()->refroidissement()->generateurs()
            ->map(fn(Generateur $item) => new GenerateurInput($context, $item))
            ->values();
        $this->systemes = $context->ressource()->refroidissement()->systemes()
            ->map(fn(Systeme $item) => new SystemeInput($context, $item))
            ->values();
    }

    // * Données calculées

    public function besoin_rule(): BesoinRefroidissementRule
    {
        return $this->require(BesoinRefroidissementRule::class);
    }

    public function consommation_rule(): ConsommationRefroidissementRule
    {
        return $this->require(ConsommationRefroidissementRule::class);
    }

    public function bfr(?Mois $mois = null): ?float
    {
        return $mois ? $this->besoin_rule()->bfr_j($mois) : $this->besoin_rule()->bfr();
    }

    public function consommations(): ConsommationCollection
    {
        return $this->consommation_rule()->consommations();
    }

    public function cef_fr(): float
    {
        return $this->consommation_rule()->cef_fr();
    }

    public function cep_fr(): float
    {
        return $this->consommation_rule()->cep_fr();
    }

    public function eges_fr(): float
    {
        return $this->consommation_rule()->eges_fr();
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
