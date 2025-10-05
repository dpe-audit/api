<?php

namespace App\Handler\Ecs;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Systeme\Reseau\Reseau;
use App\Domain\Ecs\Systeme\Stockage\Stockage;
use App\Domain\Ecs\Systeme\Systeme;
use App\Dto\Ecs\Systeme\SystemeDto;

final class CreateSystemeHandler
{
    public function __invoke(SystemeDto $payload, Ecs $aggregate): Systeme
    {
        return Systeme::create(
            id: Id::fromString($payload->id),
            ecs: $aggregate,
            description: $payload->description,
            installation: $aggregate->installations()->find(Id::fromString($payload->installation_id)),
            generateur: $aggregate->generateurs()->find(Id::fromString($payload->generateur_id)),
            reseau: Reseau::create(
                alimentation_contigue: $payload->reseau->alimentation_contigue,
                niveaux_desservis: $payload->reseau->niveaux_desservis,
                isolation: $payload->reseau->isolation,
                bouclage: $payload->reseau->bouclage,
            ),
            stockage: Stockage::create(
                volume: $payload->stockage->volume,
                position_volume_chauffe: $payload->stockage->position_volume_chauffe,
            ),
        );
    }
}
