<?php

namespace App\Handler\Refroidissement;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Systeme\Systeme;
use App\Dto\Refroidissement\SystemeDto;

final class CreateSystemeHandler
{
    public function __invoke(SystemeDto $payload, Refroidissement $aggregate): Systeme
    {
        return Systeme::create(
            id: Id::fromString($payload->id),
            refroidissement: $aggregate,
            description: $payload->description,
            installation: $aggregate->installations()->find(Id::fromString($payload->installation_id)),
            generateur: $aggregate->generateurs()->find(Id::fromString($payload->generateur_id)),
        );
    }
}
