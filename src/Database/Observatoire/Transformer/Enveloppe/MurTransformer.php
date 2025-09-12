<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Enveloppe\Mur\IsolationDto;
use App\Dto\Enveloppe\Mur\MurDto;
use App\Dto\Enveloppe\Mur\PositionDto;

final class MurTransformer
{
    /**
     * @return array<MurDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->enveloppe->mur_collection as $mur) {
            if (0 == $mur->surface()) {
                continue;
            }
            $collection[] = new MurDto(
                id: (string) $mur->id(),
                description: $mur->description(),
                type_structure: $mur->type_structure(),
                epaisseur_structure: $mur->epaisseur_structure,
                type_doublage: $mur->type_doublage(),
                presence_enduit_isolant: $mur->enduit_isolant_paroi_ancienne,
                paroi_ancienne: $mur->enduit_isolant_paroi_ancienne,
                inertie: $mur->inertie($ressource),
                annee_construction: null,
                annee_renovation: null,
                u0: $mur->umur0_saisi,
                u: $mur->umur_saisi,
                isolation: new IsolationDto(
                    etat: $mur->etat_isolation(),
                    type: $mur->type_isolation(),
                    annee_installation: $mur->annee_isolation($ressource),
                    epaisseur: $mur->epaisseur_isolation(),
                    resistance_thermique: $mur->resistance_isolation
                ),
                position: new PositionDto(
                    surface: $mur->surface(),
                    mitoyennete: $mur->mitoyennete(),
                    orientation: null,
                    local_non_chauffe_id: $mur->lnc_id($ressource)?->toBinary()
                )
            );
        }

        return $collection;
    }
}
