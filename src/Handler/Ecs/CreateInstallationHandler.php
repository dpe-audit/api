<?php

namespace App\Handler\Ecs;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Installation\Solaire\Solaire;
use App\Dto\Ecs\Installation\InstallationDto;

final class CreateInstallationHandler
{
    public function __invoke(InstallationDto $payload, Ecs $aggregate): Installation
    {
        return Installation::create(
            id: Id::fromString($payload->id),
            ecs: $aggregate,
            description: $payload->description,
            surface: $payload->surface,
            solaire_thermique: $payload->solaire_thermique ? Solaire::create(
                usage: $payload->solaire_thermique->usage,
                annee_installation: $payload->solaire_thermique->annee_installation,
                fecs: $payload->solaire_thermique->fecs,
            ) : null,
        );
    }
}
