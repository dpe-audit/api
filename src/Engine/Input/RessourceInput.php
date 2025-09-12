<?php

namespace App\Engine\Input;

use App\Engine\Engine;
use App\Engine\Input;
use App\Engine\Input\Chauffage\ChauffageInput;
use App\Engine\Input\Ecs\EcsInput;
use App\Engine\Input\Enveloppe\EnveloppeInput;
use App\Engine\Input\Production\ProductionInput;
use App\Engine\Input\Refroidissement\RefroidissementInput;
use App\Engine\Input\Ventilation\VentilationInput;
use App\Engine\Rules\Performance\PerformanceAuxiliairesRule;
use App\Engine\Rules\Performance\PerformanceEclairageRule;

final class RessourceInput extends Input
{
    public readonly BatimentInput $batiment;
    public readonly EnveloppeInput $enveloppe;
    public readonly ChauffageInput $chauffage;
    public readonly EcsInput $ecs;
    public readonly RefroidissementInput $refroidissement;
    public readonly VentilationInput $ventilation;
    public readonly ProductionInput $production;

    public function __construct(public readonly Engine $context)
    {
        $this->batiment = new BatimentInput($context);
        $this->enveloppe = new EnveloppeInput($context);
        $this->chauffage = new ChauffageInput($context);
        $this->ecs = new EcsInput($context);
        $this->refroidissement = new RefroidissementInput($context);
        $this->ventilation = new VentilationInput($context);
        $this->production = new ProductionInput($context);
    }

    // * Données calculées

    public function performance_eclairage_rule(): PerformanceEclairageRule
    {
        return $this->require(PerformanceEclairageRule::class);
    }

    public function performance_auxiliaires_rule(): PerformanceAuxiliairesRule
    {
        return $this->require(PerformanceAuxiliairesRule::class);
    }

    public function cef_chauffage(): float
    {
        return $this->chauffage->cef();
    }

    public function cep_chauffage(): float
    {
        return $this->chauffage->cep();
    }

    public function eges_chauffage(): float
    {
        return $this->chauffage->eges();
    }

    public function cef_ecs(): float
    {
        return $this->ecs->cef();
    }

    public function cep_ecs(): float
    {
        return $this->ecs->cep();
    }

    public function eges_ecs(): float
    {
        return $this->ecs->eges();
    }

    public function cef_refroidissement(): float
    {
        return $this->refroidissement->cef();
    }

    public function cep_refroidissement(): float
    {
        return $this->refroidissement->cep();
    }

    public function eges_refroidissement(): float
    {
        return $this->refroidissement->eges();
    }

    public function cef_eclairage(): float
    {
        return $this->performance_eclairage_rule()->cef();
    }

    public function cep_eclairage(): float
    {
        return $this->performance_eclairage_rule()->cep();
    }

    public function eges_eclairage(): float
    {
        return $this->performance_eclairage_rule()->eges();
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
