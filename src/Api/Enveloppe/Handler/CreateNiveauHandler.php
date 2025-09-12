<?php

namespace App\Api\Enveloppe\Handler;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Niveau\Niveau;
use App\Dto\Enveloppe\Niveau\NiveauDto;

final class CreateNiveauHandler
{
    public function __invoke(NiveauDto $payload, Enveloppe $entity): Niveau
    {
        return Niveau::create(
            id: Id::fromString($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            surface: $payload->surface,
            inertie_paroi_verticale: $payload->inertie_paroi_verticale,
            inertie_plancher_haut: $payload->inertie_plancher_haut,
            inertie_plancher_bas: $payload->inertie_plancher_bas,
        );
    }
}
