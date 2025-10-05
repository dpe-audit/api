<?php

namespace App\Engine\Input;

use App\Engine\Engine;
use App\Engine\Input;
use App\Engine\Input\Chauffage\ChauffageInput;
use App\Engine\Input\Eclairage\EclairageInput;
use App\Engine\Input\Ecs\EcsInput;
use App\Engine\Input\Enveloppe\EnveloppeInput;
use App\Engine\Input\Production\ProductionInput;
use App\Engine\Input\Refroidissement\RefroidissementInput;
use App\Engine\Input\Ventilation\VentilationInput;

final class RessourceInput extends Input
{
    public readonly BatimentInput $batiment;
    public readonly EnveloppeInput $enveloppe;
    public readonly ChauffageInput $chauffage;
    public readonly EcsInput $ecs;
    public readonly RefroidissementInput $refroidissement;
    public readonly VentilationInput $ventilation;
    public readonly ProductionInput $production;
    public readonly EclairageInput $eclairage;

    public function __construct(public readonly Engine $context)
    {
        $this->batiment = new BatimentInput($context);
        $this->enveloppe = new EnveloppeInput($context);
        $this->chauffage = new ChauffageInput($context);
        $this->ecs = new EcsInput($context);
        $this->refroidissement = new RefroidissementInput($context);
        $this->ventilation = new VentilationInput($context);
        $this->production = new ProductionInput($context);
        $this->eclairage = new EclairageInput($context);
    }

    // * Données calculées

}
