<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Porte\Menuiserie\Menuiserie;
use App\Domain\Enveloppe\Porte\Porte;
use App\Domain\Enveloppe\Porte\Position\Position;
use App\Domain\Enveloppe\Porte\Vitrage\Vitrage;
use App\Dto\Enveloppe\Porte\PorteDto;
use Webmozart\Assert\Assert;

final class CreatePorteHandler
{
    public function __invoke(PorteDto $payload, Enveloppe $aggregate): Porte
    {
        if ($payload->position->paroi_id) {
            Assert::notNull($aggregate->parois()->find(Id::fromString($payload->position->paroi_id)));
        }
        if ($payload->position->local_non_chauffe_id) {
            Assert::notNull($aggregate->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id)));
        }
        return Porte::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            isolation: $payload->isolation,
            materiau: $payload->materiau,
            annee_installation: $payload->annee_installation,
            u: $payload->u,
            position: Position::create(
                type_pose: $payload->position->type_pose,
                surface: $payload->position->surface,
                presence_sas: $payload->position->presence_sas,
                mitoyennete: $payload->position->mitoyennete,
                orientation: $payload->position->orientation,
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $aggregate->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id))
                    : null,
                paroi: $payload->position->paroi_id
                    ? $aggregate->parois()->find(Id::fromString($payload->position->paroi_id))
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
