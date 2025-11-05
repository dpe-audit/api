<?php

namespace App\Engine\Input\Eclairage;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Eclairage\Eclairage;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Eclairage\ConsommationEclairageRule;

final class EclairageInput extends Input
{
    public function __construct(public readonly Eclairage $entity, public readonly Engine $context)
    {
        $this->entity = $entity;
    }

    // * Données calculées

    public function consommation_rule(): ConsommationEclairageRule
    {
        return $this->require(ConsommationEclairageRule::class);
    }

    public function consommations(): ConsommationCollection
    {
        return $this->consommation_rule()->consommations();
    }

    public function cef_ecl(): float
    {
        return $this->consommation_rule()->cef_ecl();
    }

    public function cep_ecl(): float
    {
        return $this->consommation_rule()->cep_ecl();
    }

    public function eges_ecl(): float
    {
        return $this->consommation_rule()->eges_ecl();
    }
}
