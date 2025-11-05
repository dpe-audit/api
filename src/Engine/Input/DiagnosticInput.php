<?php

namespace App\Engine\Input;

use App\Domain\Diagnostic\Diagnostic;
use App\Engine\Engine;
use App\Engine\Input;
use App\Engine\Input\Chauffage\ChauffageInput;
use App\Engine\Input\Eclairage\EclairageInput;
use App\Engine\Input\Ecs\EcsInput;
use App\Engine\Input\Enveloppe\EnveloppeInput;
use App\Engine\Input\Production\ProductionInput;
use App\Engine\Input\Refroidissement\RefroidissementInput;
use App\Engine\Input\Ventilation\VentilationInput;

final class DiagnosticInput extends Input
{
    public readonly BatimentInput $batiment;
    public readonly EnveloppeInput $enveloppe;
    public readonly ChauffageInput $chauffage;
    public readonly EcsInput $ecs;
    public readonly RefroidissementInput $refroidissement;
    public readonly VentilationInput $ventilation;
    public readonly ProductionInput $production;
    public readonly EclairageInput $eclairage;

    public function __construct(public readonly Diagnostic $entity, public readonly Engine $context)
    {
        $this->batiment = new BatimentInput($context);
        $this->enveloppe = new EnveloppeInput(entity: $entity->enveloppe(), context: $context);
        $this->chauffage = new ChauffageInput(entity: $entity->chauffage(), context: $context);
        $this->ecs = new EcsInput(entity: $entity->ecs(), context: $context);
        $this->refroidissement = new RefroidissementInput(entity: $entity->refroidissement(), context: $context);
        $this->ventilation = new VentilationInput(entity: $entity->ventilation(), context: $context);
        $this->production = new ProductionInput(entity: $entity->production(), context: $context);
        $this->eclairage = new EclairageInput(entity: $entity->eclairage(), context: $context);
    }
}
