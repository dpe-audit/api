<?php

namespace App\Handler\Diagnostic;

use App\Domain\Diagnostic\Diagnostic;
use App\Dto\Diagnostic\DiagnosticDto;
use App\Handler\Chauffage\CreateChauffageHandler;
use App\Handler\Ecs\CreateEcsHandler;
use App\Handler\Enveloppe\CreateEnveloppeHandler;
use App\Handler\Logement\CreateLogementHandler;
use App\Handler\Production\CreateProductionHandler;
use App\Handler\Refroidissement\CreateRefroidissementHandler;
use App\Handler\Ventilation\CreateVentilationHandler;

final class CreateDiagnosticHandler
{
    public function __construct(
        private readonly CreateEnveloppeHandler $createEnveloppeHandler,
        private readonly CreateLogementHandler $createLogementHandler,
        private readonly CreateVentilationHandler $createVentilationHandler,
        private readonly CreateRefroidissementHandler $createRefroidissementHandler,
        private readonly CreateChauffageHandler $createChauffageHandler,
        private readonly CreateEcsHandler $createEcsHandler,
        private readonly CreateProductionHandler $createProductionHandler,
    ) {}

    public function __invoke(DiagnosticDto $payload): Diagnostic
    {
        $entity = Diagnostic::create(
            date_visite: $payload->date_visite,
            date_etablissement: $payload->date_etablissement,
            batiment: $payload->batiment->to(),
            enveloppe: $this->createEnveloppeHandler->__invoke($payload->enveloppe),
            ventilation: $this->createVentilationHandler->__invoke($payload->ventilation),
            refroidissement: $this->createRefroidissementHandler->__invoke($payload->refroidissement),
            chauffage: $this->createChauffageHandler->__invoke($payload->chauffage),
            ecs: $this->createEcsHandler->__invoke($payload->ecs),
            production: $this->createProductionHandler->__invoke($payload->production),
        );

        foreach ($payload->logements as $item) {
            $entity->add_logement($this->createLogementHandler->__invoke($item, $entity));
        }
        return $entity;
    }
}
