<?php

namespace App\Api\Enveloppe\Handler;

use App\Api\Enveloppe\Model\Lnc as Payload;
use App\Model\Common\ValueObject\{Id, Inclinaison, Orientation};
use App\Model\Enveloppe\Entity\Lnc;
use App\Model\Enveloppe\Entity\Lnc\{Baie, ParoiOpaque};
use App\Model\Enveloppe\Enveloppe;
use App\Model\Enveloppe\ValueObject\Lnc\{PositionBaie, PositionParoi};

final class CreateLncHandler
{
    public function __invoke(Payload $payload, Enveloppe $entity): Lnc
    {
        $local_non_chauffe = Lnc::create(
            id: Id::from($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            type: $payload->type,
        );

        $this->create_parois_opaques($payload, $local_non_chauffe);
        $this->create_baies($payload, $local_non_chauffe);

        return $local_non_chauffe;
    }

    private function create_parois_opaques(Payload $payload, Lnc $entity): void
    {
        foreach ($payload->parois_opaques as $paroi_payload) {
            $entity->add_paroi_opaque(ParoiOpaque::create(
                id: Id::from($paroi_payload->id),
                local_non_chauffe: $entity,
                description: $paroi_payload->description,
                isolation: $paroi_payload->isolation,
                position: PositionParoi::create(
                    mitoyennete: $paroi_payload->position->mitoyennete,
                    surface: $paroi_payload->position->surface,
                )
            ));
        }
    }

    private function create_baies(Payload $payload, Lnc $entity): void
    {
        foreach ($payload->baies as $paroi_payload) {
            $entity->add_baie(Baie::create(
                id: Id::from($paroi_payload->id),
                local_non_chauffe: $entity,
                description: $paroi_payload->description,
                type: $paroi_payload->type_baie,
                materiau: $paroi_payload->materiau,
                type_vitrage: $paroi_payload->type_vitrage,
                presence_rupteur_pont_thermique: $paroi_payload->presence_rupteur_pont_thermique,
                position: PositionBaie::create(
                    mitoyennete: $paroi_payload->position->mitoyennete,
                    surface: $paroi_payload->position->surface,
                    inclinaison: Inclinaison::from($paroi_payload->position->inclinaison),
                    orientation: $paroi_payload->position->orientation
                        ? Orientation::from($paroi_payload->position->orientation)
                        : null,
                    paroi: $paroi_payload->position->paroi_id
                        ? $entity->parois_opaques()->find(Id::from($paroi_payload->position->paroi_id))
                        : null,
                )
            ));
        }
    }
}
