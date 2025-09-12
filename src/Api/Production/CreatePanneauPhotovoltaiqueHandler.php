<?php

namespace App\Api\Production;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\PanneauPhotovoltaique\PanneauPhotovoltaique;
use App\Domain\Production\Production;
use App\Dto\Production\PanneauPhotovoltaiqueDto;

final class CreatePanneauPhotovoltaiqueHandler
{
    public function __invoke(PanneauPhotovoltaiqueDto $payload, Production $production): PanneauPhotovoltaique
    {
        return PanneauPhotovoltaique::create(
            id: Id::fromString($payload->id),
            production: $production,
            description: $payload->description,
            orientation: $payload->orientation,
            inclinaison: $payload->inclinaison,
            modules: $payload->modules,
            installation_collective: $payload->installation_collective,
            surface: $payload->surface,
        );
    }
}
