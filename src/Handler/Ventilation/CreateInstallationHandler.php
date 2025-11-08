<?php

namespace App\Handler\Ventilation;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Installation\Installation;
use App\Domain\Ventilation\Ventilation;
use App\Dto\Ventilation\InstallationDto;
use Webmozart\Assert\Assert;

final class CreateInstallationHandler
{
    public function __invoke(InstallationDto $payload, Ventilation $aggregate): Installation
    {
        $generateur = null;

        if ($payload->generateur_id) {
            $generateur = $aggregate->generateurs()->find(Id::fromString($payload->generateur_id));
            Assert::notNull($generateur);
        }
        return Installation::create(
            id: Id::fromString($payload->id),
            ventilation: $aggregate,
            description: $payload->description,
            surface: $payload->surface,
            type: $payload->type,
            generateur: $generateur,
        );
    }
}
