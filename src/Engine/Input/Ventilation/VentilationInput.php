<?php

namespace App\Engine\Input\Ventilation;

use App\Domain\Ventilation\Generateur\Generateur;
use App\Domain\Ventilation\Installation\Installation;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Performance\PerformanceAuxiliairesVentilationRule;

final class VentilationInput extends Input
{
    /** @var InstallationInput[] */
    public readonly array $installations;
    /** @var GenerateurInput[] */
    public readonly array $generateurs;

    public function __construct(public readonly Engine $context)
    {
        $this->installations = $context->ressource()->ventilation()->installations()
            ->map(fn(Installation $item) => new InstallationInput($context, $item))
            ->values();
        $this->generateurs = $context->ressource()->ventilation()->generateurs()
            ->map(fn(Generateur $item) => new GenerateurInput($context, $item))
            ->values();
    }

    public function consommation_auxiliaire_rule(): PerformanceAuxiliairesVentilationRule
    {
        return $this->require(PerformanceAuxiliairesVentilationRule::class);
    }

    public function cef_auxiliaires(): float
    {
        return $this->consommation_auxiliaire_rule()->cef();
    }

    public function cep_auxiliaires(): float
    {
        return $this->consommation_auxiliaire_rule()->cep();
    }

    public function eges_auxiliaires(): float
    {
        return $this->consommation_auxiliaire_rule()->eges();
    }
}
