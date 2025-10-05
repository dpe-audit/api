<?php

namespace App\Handler\Ventilation;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Installation\Installation;
use App\Domain\Ventilation\Ventilation;
use App\Dto\Ventilation\InstallationDto;

final class CreateInstallationHandler
{
    public function __invoke(InstallationDto $payload, Ventilation $entity): Installation
    {
        return Installation::create(
            id: Id::fromString($payload->id),
            ventilation: $entity,
            description: $payload->description,
            surface: $payload->surface,
            type: $payload->type,
            generateur: $entity->generateurs()->find(Id::fromString($payload->generateur_id)),
        );
    }
}
