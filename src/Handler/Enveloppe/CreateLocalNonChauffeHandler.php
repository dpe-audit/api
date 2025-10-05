<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Lnc\Lnc;
use App\Domain\Enveloppe\Lnc\Baie\{Baie, Position as BaiePosition};
use App\Domain\Enveloppe\Lnc\Paroi\{Paroi, Position as ParoiPosition};
use App\Dto\Enveloppe\Lnc\LncDto;
use App\Dto\Enveloppe\Lnc\Paroi\ParoiDto;
use App\Dto\Enveloppe\Lnc\Baie\BaieDto;

final class CreateLocalNonChauffeHandler
{
    private function createParoi(ParoiDto $payload, Lnc $entity): Paroi
    {
        return Paroi::create(
            id: Id::fromString($payload->id),
            local_non_chauffe: $entity,
            description: $payload->description,
            isolation: $payload->isolation,
            position: ParoiPosition::create(
                mitoyennete: $payload->position->mitoyennete,
                surface: $payload->position->surface,
            ),
        );
    }

    private function createBaie(BaieDto $payload, Lnc $entity): Baie
    {
        return Baie::create(
            id: Id::fromString($payload->id),
            local_non_chauffe: $entity,
            description: $payload->description,
            type_vitrage: $payload->type_vitrage,
            materiau: $payload->materiau,
            presence_rupteur_pont_thermique: $payload->presence_rupteur_pont_thermique,
            position: BaiePosition::create(
                mitoyennete: $payload->position->mitoyennete,
                surface: $payload->position->surface,
                inclinaison: $payload->position->inclinaison,
                orientation: $payload->position->orientation,
            ),
        );
    }

    public function __invoke(LncDto $payload, Enveloppe $aggregate): Lnc
    {
        $entity = Lnc::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            type: $payload->type,
        );

        foreach ($payload->parois as $paroi) {
            $entity->add_paroi($this->createParoi($paroi, $entity));
        }
        foreach ($payload->baies as $baie) {
            $entity->add_baie($this->createBaie($baie, $entity));
        }
        return $entity;
    }
}
