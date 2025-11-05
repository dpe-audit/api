<?php

namespace App\Handler\Scenario;

use App\Domain\Scenario\Etape\Etape;
use App\Domain\Scenario\Scenario;
use App\Dto\Scenario\EtapeDto;
use App\Handler\Chauffage\CreateChauffageHandler;
use App\Handler\Ecs\CreateEcsHandler;
use App\Handler\Enveloppe\CreateEnveloppeHandler;
use App\Handler\Logement\CreateLogementHandler;
use App\Handler\Production\CreateProductionHandler;
use App\Handler\Refroidissement\CreateRefroidissementHandler;
use App\Handler\Ventilation\CreateVentilationHandler;

final class CreateEtapeHandler
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

    public function __invoke(EtapeDto $payload, Scenario $entity): Etape
    {
        return Etape::create(
            scenario: $entity,
            nom: $payload->nom,
            description: $payload->description,
            enveloppe: $this->createEnveloppeHandler->__invoke($payload->enveloppe),
            chauffage: $this->createChauffageHandler->__invoke($payload->chauffage),
            ecs: $this->createEcsHandler->__invoke($payload->ecs),
            refroidissement: $this->createRefroidissementHandler->__invoke($payload->refroidissement),
            ventilation: $this->createVentilationHandler->__invoke($payload->ventilation),
            production: $this->createProductionHandler->__invoke($payload->production),
        );
    }
}
