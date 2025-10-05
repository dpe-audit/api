<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Baie\Baie;
use App\Domain\Enveloppe\Baie\Menuiserie\Menuiserie;
use App\Domain\Enveloppe\Baie\Position\Position;
use App\Domain\Enveloppe\Baie\Survitrage\Survitrage;
use App\Domain\Enveloppe\Baie\Vitrage\Vitrage;
use App\Domain\Enveloppe\Enveloppe;
use App\Dto\Enveloppe\Baie\BaieDto;
use Webmozart\Assert\Assert;

final class CreateBaieHandler
{
    public function __invoke(BaieDto $payload, Enveloppe $aggregate): Baie
    {
        if ($payload->position->paroi_id) {
            Assert::notNull($aggregate->parois()->find(Id::fromString($payload->position->paroi_id)));
        }
        if ($payload->position->local_non_chauffe_id) {
            Assert::notNull($aggregate->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id)));
        }
        if ($payload->position->double_fenetre_id) {
            Assert::notNull($aggregate->doubles_fenetres()->find(Id::fromString($payload->position->double_fenetre_id)));
        }

        return Baie::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            type: $payload->type,
            presence_protection_solaire: $payload->presence_protection_solaire,
            type_fermeture: $payload->type_fermeture,
            annee_installation: $payload->annee_installation,
            ug: $payload->ug,
            uw: $payload->uw,
            ujn: $payload->ujn,
            sw: $payload->sw,
            position: Position::create(
                surface: $payload->position->surface,
                mitoyennete: $payload->position->mitoyennete,
                inclinaison: $payload->position->inclinaison,
                orientation: $payload->position->orientation,
                type_pose: $payload->position->type_pose,
                presence_soubassement: $payload->position->presence_soubassement,
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $aggregate->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id))
                    : null,
                paroi: $payload->position->paroi_id
                    ? $aggregate->parois()->find(Id::fromString($payload->position->paroi_id))
                    : null,
                double_fenetre: $payload->position->double_fenetre_id
                    ? $aggregate->doubles_fenetres()->find(Id::fromString($payload->position->double_fenetre_id))
                    : null,
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
