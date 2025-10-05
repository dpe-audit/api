<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Masque\Masque;
use App\Dto\Enveloppe\Masque\MasqueDto;

final class CreateMasqueHandler
{
    public function __invoke(MasqueDto $payload, Enveloppe $aggregate): Masque
    {
        return Masque::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            type: $payload->type,
            configuration: $payload->configuration,
            hauteur: $payload->hauteur,
            profondeur: $payload->profondeur,
            orientation: $payload->orientation,
            secteur: $payload->secteur,
        );
    }
}
