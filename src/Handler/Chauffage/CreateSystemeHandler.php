<?php

namespace App\Handler\Chauffage;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Systeme\Reseau\Reseau;
use App\Domain\Chauffage\Systeme\Systeme;
use App\Dto\Chauffage\Systeme\SystemeDto;

final class CreateSystemeHandler
{
    public function __invoke(SystemeDto $payload, Chauffage $aggregate): Systeme
    {
        $entity = Systeme::create(
            id: Id::fromString($payload->id),
            chauffage: $aggregate,
            description: $payload->description,
            type: $payload->type,
            installation: $aggregate->installations()->find(Id::fromString($payload->installation_id)),
            generateur: $aggregate->generateurs()->find(Id::fromString($payload->generateur_id)),
            reseau: $payload->reseau ? Reseau::create(
                type_distribution: $payload->reseau->type_distribution,
                presence_circulateur_externe: $payload->reseau->presence_circulateur_externe,
                niveaux_desservis: $payload->reseau->niveaux_desservis,
                isolation: $payload->reseau->isolation,
            ) : null,
        );

        foreach ($payload->emetteurs as $id) {
            $entity->reference_emetteur($aggregate->emetteurs()->find(Id::fromString($id)));
        }
        return $entity;
    }
}
