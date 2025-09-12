<?php

namespace App\Api\Enveloppe\Handler;

use App\Api\Enveloppe\Model\Mur as Payload;
use App\Model\Common\ValueObject\{Annee, Id, Orientation};
use App\Model\Enveloppe\Entity\Mur;
use App\Model\Enveloppe\Enveloppe;
use App\Model\Enveloppe\ValueObject\Isolation;
use App\Model\Enveloppe\ValueObject\Mur\Position;

final class CreateMurHandler
{
    public function __invoke(Payload $payload, Enveloppe $entity): Mur
    {
        return Mur::create(
            id: Id::from($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            type_structure: $payload->type_structure,
            epaisseur_structure: $payload->epaisseur_structure,
            type_doublage: $payload->type_doublage,
            presence_enduit_isolant: $payload->presence_enduit_isolant,
            paroi_ancienne: $payload->paroi_ancienne,
            inertie: $payload->inertie,
            annee_construction: $payload->annee_construction ? Annee::from($payload->annee_construction) : null,
            annee_renovation: $payload->annee_renovation ? Annee::from($payload->annee_renovation) : null,
            u0: $payload->u0,
            u: $payload->u,
            position: Position::create(
                surface: $payload->position->surface,
                mitoyennete: $payload->position->mitoyennete,
                orientation: Orientation::from($payload->position->orientation),
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $entity->locaux_non_chauffes()->find(Id::from($payload->position->local_non_chauffe_id))
                    : null,
            ),
            isolation: Isolation::create(
                etat_isolation: $payload->isolation->etat_isolation,
                type_isolation: $payload->isolation->type_isolation,
                epaisseur_isolation: $payload->isolation->epaisseur_isolation,
                resistance_thermique_isolation: $payload->isolation->resistance_thermique_isolation,
                annee_isolation: $payload->isolation->annee_isolation
                    ? Annee::from($payload->isolation->annee_isolation)
                    : null,
            ),
        );
    }
}
