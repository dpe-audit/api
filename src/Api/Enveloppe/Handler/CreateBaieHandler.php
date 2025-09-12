<?php

namespace App\Api\Enveloppe\Handler;

use App\Api\Enveloppe\Model\Baie as Payload;
use App\Model\Common\ValueObject\{Annee, Id, Inclinaison, Orientation};
use App\Model\Enveloppe\Entity\Baie;
use App\Model\Enveloppe\Entity\Baie\DoubleFenetre;
use App\Model\Enveloppe\Enveloppe;
use App\Model\Enveloppe\ValueObject\Baie\{Composition, Menuiserie, Performance, Position, Survitrage, Vitrage};

final class CreateBaieHandler
{
    public function __invoke(Payload $payload, Enveloppe $entity): Baie
    {
        $double_fenetre = $this->create_double_fenetre($payload);

        return Baie::create(
            id: Id::from($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            presence_protection_solaire: $payload->presence_protection_solaire,
            type_fermeture: $payload->type_fermeture,
            annee_installation: $payload->annee_installation ? Annee::from($payload->annee_installation) : null,
            composition: Composition::create(
                type_baie: $payload->type_baie,
                type_pose: $payload->type_pose,
                materiau: $payload->materiau,
                presence_soubassement: $payload->presence_soubassement,
                vitrage: $payload->vitrage ? Vitrage::create(
                    type_vitrage: $payload->vitrage->type_vitrage,
                    nature_gaz_lame: $payload->vitrage->nature_lame,
                    epaisseur_lame: $payload->vitrage->epaisseur_lame,
                    survitrage: $payload->vitrage->survitrage ? Survitrage::create(
                        type_survitrage: $payload->vitrage->survitrage->type_survitrage,
                        epaisseur_lame: $payload->vitrage->survitrage->epaisseur_lame,
                    ) : null,
                ) : null,
                menuiserie: $payload->menuiserie ? Menuiserie::create(
                    largeur_dormant: $payload->menuiserie->largeur_dormant,
                    presence_joint: $payload->menuiserie->presence_joint,
                    presence_retour_isolation: $payload->menuiserie->presence_retour_isolation,
                    presence_rupteur_pont_thermique: $payload->menuiserie->presence_rupteur_pont_thermique,
                ) : null,
            ),
            performance: Performance::create(
                ug: $payload->ug,
                uw: $payload->uw,
                ujn: $payload->ujn,
                sw: $payload->sw,
            ),
            double_fenetre: $double_fenetre,
            position: Position::create(
                surface: $payload->position->surface,
                mitoyennete: $payload->position->mitoyennete,
                inclinaison: Inclinaison::from($payload->position->inclinaison),
                orientation: $payload->position->orientation
                    ? Orientation::from($payload->position->orientation)
                    : null,
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $entity->locaux_non_chauffes()->find(Id::from($payload->position->local_non_chauffe_id))
                    : null,
                paroi: $payload->position->paroi_id
                    ? $entity->paroi(Id::from($payload->position->paroi_id))
                    : null,
            ),
        );
    }

    private function create_double_fenetre(Payload $payload): ?DoubleFenetre
    {
        if (null === $double_fenetre_payload = $payload->double_fenetre) {
            return null;
        }
        return DoubleFenetre::create(
            composition: Composition::create(
                type_baie: $double_fenetre_payload->type_baie,
                type_pose: $double_fenetre_payload->type_pose,
                materiau: $double_fenetre_payload->materiau,
                presence_soubassement: $double_fenetre_payload->presence_soubassement,
                vitrage: $double_fenetre_payload->vitrage ? Vitrage::create(
                    type_vitrage: $double_fenetre_payload->vitrage->type_vitrage,
                    nature_gaz_lame: $double_fenetre_payload->vitrage->nature_lame,
                    epaisseur_lame: $double_fenetre_payload->vitrage->epaisseur_lame,
                    survitrage: $double_fenetre_payload->vitrage->survitrage ? Survitrage::create(
                        type_survitrage: $double_fenetre_payload->vitrage->survitrage->type_survitrage,
                        epaisseur_lame: $double_fenetre_payload->vitrage->survitrage->epaisseur_lame,
                    ) : null,
                ) : null,
                menuiserie: $double_fenetre_payload->menuiserie ? Menuiserie::create(
                    largeur_dormant: $double_fenetre_payload->menuiserie->largeur_dormant,
                    presence_joint: $double_fenetre_payload->menuiserie->presence_joint,
                    presence_retour_isolation: $double_fenetre_payload->menuiserie->presence_retour_isolation,
                    presence_rupteur_pont_thermique: $double_fenetre_payload->menuiserie->presence_rupteur_pont_thermique,
                ) : null,
            ),
            performance: Performance::create(
                ug: $double_fenetre_payload->ug,
                uw: $double_fenetre_payload->uw,
                sw: $double_fenetre_payload->sw,
            ),
        );
    }
}
