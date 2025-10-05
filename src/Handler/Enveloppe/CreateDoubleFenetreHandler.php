<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\DoubleFenetre\DoubleFenetre;
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Menuiserie;
use App\Domain\Enveloppe\DoubleFenetre\Position\Position;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\Survitrage;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\Vitrage;
use App\Domain\Enveloppe\Enveloppe;
use App\Dto\Enveloppe\DoubleFenetre\DoubleFenetreDto;

final class CreateDoubleFenetreHandler
{
    public function __invoke(DoubleFenetreDto $payload, Enveloppe $aggregate): DoubleFenetre
    {
        return DoubleFenetre::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            type: $payload->type,
            ug: $payload->ug,
            uw: $payload->uw,
            sw: $payload->sw,
            position: Position::create(
                inclinaison: $payload->position->inclinaison,
                type_pose: $payload->position->type_pose,
                presence_soubassement: $payload->position->presence_soubassement,
            ),
            vitrage: Vitrage::create(
                type: $payload->vitrage->type,
                nature_lame: $payload->vitrage->nature_lame,
                epaisseur_lame: $payload->vitrage->epaisseur_lame,
            ),
            survitrage: $payload->survitrage ? Survitrage::create(
                type: $payload->survitrage->type,
                epaisseur_lame: $payload->survitrage->epaisseur_lame,
            ) : null,
            menuiserie: $payload->menuiserie ? Menuiserie::create(
                largeur_dormant: $payload->menuiserie->largeur_dormant,
                presence_joint: $payload->menuiserie->presence_joint,
                presence_retour_isolation: $payload->menuiserie->presence_retour_isolation,
                presence_rupteur_pont_thermique: $payload->menuiserie->presence_rupteur_pont_thermique,
            ) : null,
        );
    }
}
