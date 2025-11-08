<?php

namespace App\Handler\Production;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\PanneauPhotovoltaique\PanneauPhotovoltaique;
use App\Domain\Production\Production;
use App\Dto\Production\PanneauPhotovoltaiqueDto;

final class CreatePanneauPhotovoltaiqueHandler
{
    public function __invoke(PanneauPhotovoltaiqueDto $payload, Production $aggregate): PanneauPhotovoltaique
    {
        return PanneauPhotovoltaique::create(
            id: Id::fromString($payload->id),
            production: $aggregate,
            description: $payload->description,
            orientation: $payload->orientation,
            inclinaison: $payload->inclinaison,
            modules: $payload->modules,
            installation_collective: $payload->installation_collective,
            surface: $payload->surface,
        );
    }
}
