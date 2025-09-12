<?php

namespace App\Engine\Input\Refroidissement;

use App\Domain\Common\Enum\Mois;
use App\Domain\Refroidissement\Generateur\Generateur;
use App\Domain\Refroidissement\Installation\Installation;
use App\Domain\Refroidissement\Systeme\Systeme;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Performance\PerformanceRefroidissementRule;
use App\Engine\Rules\Refroidissement\BesoinRefroidissementRule;

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

    public function consommation_rule(): PerformanceRefroidissementRule
    {
        return $this->require(PerformanceRefroidissementRule::class);
    }

    public function bfr(?Mois $mois = null): ?float
    {
        return $mois ? $this->besoin_rule()->bfr_j($mois) : $this->besoin_rule()->bfr();
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
