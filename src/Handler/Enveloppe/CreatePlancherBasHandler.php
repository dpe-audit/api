<?php

namespace App\Handler\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Paroi\Isolation\Isolation;
use App\Domain\Enveloppe\PlancherBas\PlancherBas;
use App\Domain\Enveloppe\PlancherBas\Position\Position;
use App\Dto\Enveloppe\PlancherBas\PlancherBasDto;
use Webmozart\Assert\Assert;

final class CreatePlancherBasHandler
{
    public function __invoke(PlancherBasDto $payload, Enveloppe $aggregate): PlancherBas
    {
        if ($payload->position->local_non_chauffe_id) {
            Assert::notNull($aggregate->locaux_non_chauffes()->find(Id::fromString($payload->position->local_non_chauffe_id)));
        }
        return PlancherBas::create(
            id: Id::fromString($payload->id),
            enveloppe: $aggregate,
            description: $payload->description,
            type_structure: $payload->type_structure,
            inertie: $payload->inertie,
            annee_construction: $payload->annee_construction,
            annee_renovation: $payload->annee_renovation,
            u0: $payload->u0,
            u: $payload->u,
            position: Position::create(
                surface: $payload->position->surface,
                surface_ue: $payload->position->surface_ue,
                perimetre_ue: $payload->position->perimetre_ue,
                mitoyennete: $payload->position->mitoyennete,
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
