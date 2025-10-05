<?php

namespace App\Handler\Logement;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Logement\Logement;
use App\Domain\Ressource\Ressource;
use App\Dto\Logement\LogementDto;

final class CreateLogementHandler
{
    public function __invoke(LogementDto $payload, Ressource $entity): Logement
    {
        return Logement::create(
            id: Id::fromString($payload->id),
            ressource: $entity,
            description: $payload->description,
            surface_habitable: $payload->surface_habitable,
            hauteur_sous_plafond: $payload->hauteur_sous_plafond,
            position: $payload->position,
            typologie: $payload->typologie,
        );
    }
}
