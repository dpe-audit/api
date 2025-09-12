<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Enveloppe\PlancherBas\IsolationDto;
use App\Dto\Enveloppe\PlancherBas\PlancherBasDto;
use App\Dto\Enveloppe\PlancherBas\PositionDto;

final class PlancherBasTransformer
{
    /**
     * @return array<PlancherBasDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->enveloppe->plancher_bas_collection as $plancher_bas) {
            if (0 == $plancher_bas->surface()) {
                continue;
            }
            $collection[] = new PlancherBasDto(
                id: (string) $plancher_bas->id(),
                description: $plancher_bas->description(),
                type_structure: $plancher_bas->type_structure(),
                inertie: $plancher_bas->inertie($ressource),
                annee_construction: null,
                annee_renovation: null,
                u0: $plancher_bas->upb0_saisi,
                u: $plancher_bas->upb_saisi,
                isolation: new IsolationDto(
                    etat: $plancher_bas->etat_isolation(),
                    type: $plancher_bas->type_isolation(),
                    annee_installation: $plancher_bas->annee_isolation($ressource),
                    epaisseur: $plancher_bas->epaisseur_isolation(),
                    resistance_thermique: $plancher_bas->resistance_isolation
                ),
                position: new PositionDto(
                    surface: $plancher_bas->surface(),
                    mitoyennete: $plancher_bas->mitoyennete(),
                    surface_ue: $plancher_bas->surface_ue,
                    perimetre_ue: $plancher_bas->perimetre_ue,
                    local_non_chauffe_id: $plancher_bas->lnc_id($ressource)?->toBinary()
                )
            );
        }

        return $collection;
    }
}
