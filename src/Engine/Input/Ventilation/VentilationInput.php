<?php

namespace App\Engine\Input\Ventilation;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Ventilation\Generateur\Generateur;
use App\Domain\Ventilation\Installation\Installation;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Ventilation\ConsommationVentilationRule;

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

    public function consommation_rule(): ConsommationVentilationRule
    {
        return $this->require(ConsommationVentilationRule::class);
    }

    public function consommations(): ConsommationCollection
    {
        return $this->consommation_rule()->consommations();
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
