<?php

namespace App\Api\Enveloppe\Handler;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Porte\Menuiserie\Menuiserie;
use App\Domain\Enveloppe\Porte\Porte;
use App\Domain\Enveloppe\Porte\Position\Position;
use App\Domain\Enveloppe\Porte\Vitrage\Vitrage;
use App\Dto\Enveloppe\Porte\PorteDto;

final class CreatePorteHandler
{
    public function __invoke(PorteDto $payload, Enveloppe $entity): Porte
    {
        return Porte::create(
            id: Id::fromString($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            type_pose: $payload->type_pose,
            isolation: $payload->isolation,
            materiau: $payload->materiau,
            annee_installation: $payload->annee_installation,
            u: $payload->u,
            position: Position::create(
                surface: $payload->position->surface,
                presence_sas: $payload->position->presence_sas,
                mitoyennete: $payload->position->mitoyennete,
                orientation: $payload->position->orientation,
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $entity->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id))
                    : null,
                paroi: $payload->position->paroi_id
                    ? $entity->parois()->find(Id::fromString($payload->position->paroi_id))
                    : null,
            ),
            vitrage: Vitrage::create(
                surface: $payload->vitrage->surface,
                type: $payload->vitrage->type,
            ),
            menuiserie: Menuiserie::create(
                presence_joint: $payload->menuiserie->presence_joint,
                presence_retour_isolation: $payload->menuiserie->presence_retour_isolation,
                largeur_dormant: $payload->menuiserie->largeur_dormant,
            )
        );
    }
}
