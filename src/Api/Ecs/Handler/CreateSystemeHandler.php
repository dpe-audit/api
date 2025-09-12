<?php

namespace App\Api\Ecs\Handler;

use App\Api\Ecs\Model\Systeme as Payload;
use App\Model\Common\ValueObject\Id;
use App\Model\Ecs\Ecs;
use App\Model\Ecs\Entity\Systeme;
use App\Model\Ecs\ValueObject\{Reseau, Stockage};

final class CreateSystemeHandler
{
    public function __invoke(Payload $payload, Ecs $entity): Systeme
    {
        $id = Id::from($payload->installation_id);

        $installation = $entity->installations()->find($id);
        $generateur = $entity->generateurs()->find(Id::from($payload->generateur_id));

        return Systeme::create(
            id: $id,
            ecs: $entity,
            installation: $installation,
            generateur: $generateur,
            reseau: Reseau::create(
                alimentation_contigue: $payload->reseau->alimentation_contigue,
                niveaux_desservis: $payload->reseau->niveaux_desservis,
                isolation: $payload->reseau->isolation,
                bouclage: $payload->reseau->bouclage,
            ),
            stockage: $payload->stockage ? Stockage::create(
                volume: $payload->stockage->volume,
                position_volume_chauffe: $payload->stockage->position_volume_chauffe,
            ) : null,
        );
    }
}
