<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Mur\Isolation\Isolation;
use App\Domain\Enveloppe\Mur\Mur;
use App\Domain\Enveloppe\Mur\Position\Position;
use App\Dto\Enveloppe\Mur\MurDto;
use Webmozart\Assert\Assert;

final class CreateMurHandler
{
    public function __invoke(MurDto $payload, Enveloppe $aggregate): Mur
    {
        if ($payload->position->local_non_chauffe_id) {
            Assert::notNull($aggregate->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id)));
        }
        return Mur::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            type_structure: $payload->type_structure,
            epaisseur_structure: $payload->epaisseur_structure,
            type_doublage: $payload->type_doublage,
            presence_enduit_isolant: $payload->presence_enduit_isolant,
            paroi_ancienne: $payload->paroi_ancienne,
            inertie: $payload->inertie,
            annee_construction: $payload->annee_construction,
            annee_renovation: $payload->annee_renovation,
            u0: $payload->u0,
            u: $payload->u,
            position: Position::create(
                surface: $payload->position->surface,
                mitoyennete: $payload->position->mitoyennete,
                orientation: $payload->position->orientation,
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $aggregate->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id))
                    : null,
            ),
            isolation: Isolation::create(
                etat: $payload->isolation->etat,
                type: $payload->isolation->type,
                epaisseur: $payload->isolation->epaisseur,
                resistance_thermique: $payload->isolation->resistance_thermique,
                annee_installation: $payload->isolation->annee_installation,
            ),
        );
    }
}
