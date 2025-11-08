<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\PontThermique\Liaison\Liaison;
use App\Domain\Enveloppe\PontThermique\PontThermique;
use App\Dto\Enveloppe\PontThermique\PontThermiqueDto;
use Webmozart\Assert\Assert;

final class CreatePontThermiqueHandler
{
    public function __invoke(PontThermiqueDto $payload, Enveloppe $aggregate): PontThermique
    {
        if ($payload->liaison->mur_id) {
            Assert::notNull($aggregate->murs()->find(Id::fromString($payload->liaison->mur_id)));
        }
        if ($payload->liaison->plancher_id) {
            Assert::notNull($aggregate->parois()->find(Id::fromString($payload->liaison->plancher_id)));
        }
        if ($payload->liaison->ouverture_id) {
            $entity = $aggregate->baies()->find(Id::fromString($payload->liaison->ouverture_id));
            $entity = $entity ?? $aggregate->portes()->find(Id::fromString($payload->liaison->ouverture_id));
            Assert::notNull($entity);
        }
        return PontThermique::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            longueur: $payload->longueur,
            kpt: $payload->kpt,
            liaison: Liaison::create(
                type: $payload->liaison->type,
                pont_thermique_partiel: $payload->liaison->pont_thermique_partiel,
                mur: $aggregate->parois()->find(Id::fromString($payload->liaison->mur_id)),
                plancher: $payload->liaison->plancher_id
                    ? $aggregate->parois()->find(Id::fromString($payload->liaison->plancher_id))
                    : null,
                ouverture: $payload->liaison->ouverture_id
                    ? $aggregate->baies()->find(Id::fromString($payload->liaison->ouverture_id))
                    : null,
            )
        );
    }
}
