<?php

namespace App\Handler\Scenario;

use App\Domain\Audit\Audit;
use App\Domain\Scenario\Scenario;
use App\Dto\Scenario\ScenarioDto;

final class CreateScenarioHandler
{
    public function __construct(
        private readonly CreateEtapeHandler $createEtapeHandler,
    ) {}

    public function __invoke(ScenarioDto $payload, Audit $aggregate): Scenario
    {
        $entity = Scenario::create(
            audit: $aggregate,
            type: $payload->type,
            nom: $payload->nom,
            description: $payload->description,
        );

        foreach ($payload->etapes as $item) {
            $entity->add_etape($this->createEtapeHandler->__invoke($item, $entity));
        }

        return $entity;
    }
}
