<?php

namespace App\Api\Enveloppe\Handler;

use App\Api\Enveloppe\Model\PontThermique as Payload;
use App\Model\Common\ValueObject\Id;
use App\Model\Enveloppe\Entity\PontThermique;
use App\Model\Enveloppe\Enum\PontThermique\TypeLiaison;
use App\Model\Enveloppe\Enveloppe;
use App\Model\Enveloppe\ValueObject\PontThermique\Liaison;

final class CreatePontThermiqueHandler
{
    public function __invoke(Payload $payload, Enveloppe $entity): PontThermique
    {
        return PontThermique::create(
            id: Id::from($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            longueur: $payload->longueur,
            kpt: $payload->kpt,
            liaison: $this->create_liaison(payload: $payload, entity: $entity),
        );
    }

    private function create_liaison(Payload $payload, Enveloppe $entity): Liaison
    {
        $mur = $entity->murs()->find(Id::from($payload->liaison->mur_id));
        $plancher = $payload->liaison->plancher_id
            ? $entity->paroi(Id::from($payload->liaison->plancher_id))
            : null;
        $ouverture = $payload->liaison->ouverture_id
            ? $entity->paroi(Id::from($payload->liaison->ouverture_id))
            : null;

        return match ($payload->liaison->type_liaison) {
            TypeLiaison::REFEND_MUR => Liaison::create_liaison_refend_mur(
                mur: $mur,
                pont_thermique_partiel: $payload->liaison->pont_thermique_partiel,
            ),
            TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR => Liaison::create_liaison_plancher_intermediaire_mur(
                mur: $mur,
                pont_thermique_partiel: $payload->liaison->pont_thermique_partiel,
            ),
            TypeLiaison::PLANCHER_BAS_MUR => Liaison::create_liaison_plancher_bas_mur(
                mur: $mur,
                paroi: $plancher,
            ),
            TypeLiaison::PLANCHER_HAUT_MUR => Liaison::create_liaison_plancher_haut_mur(
                mur: $mur,
                paroi: $plancher,
            ),
            TypeLiaison::MENUISERIE_MUR => Liaison::create_liaison_menuiserie_mur(
                mur: $mur,
                paroi: $ouverture,
            ),
        };
    }
}
