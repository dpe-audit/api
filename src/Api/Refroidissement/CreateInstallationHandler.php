<?php

namespace App\Api\Refroidissement;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Installation\Installation;
use App\Domain\Refroidissement\Refroidissement;
use App\Dto\Refroidissement\InstallationDto;

final class CreateInstallationHandler
{
    public function __invoke(InstallationDto $payload, Refroidissement $entity): Installation
    {
        return Installation::create(
            id: Id::fromString($payload->id),
            refroidissement: $entity,
            description: $payload->description,
            surface: $payload->surface,
        );
    }
}
