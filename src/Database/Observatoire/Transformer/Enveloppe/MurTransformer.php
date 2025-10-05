<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\{XMLMur, XMLRessource};
use App\Dto\Enveloppe\Mur\{MurDto, PositionDto};
use App\Dto\Enveloppe\Paroi\IsolationDto;

final class MurTransformer
{
    public function supports(XMLMur $element): bool
    {
        return $element->surface() > 0;
    }

    /**
     * @return array<MurDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->enveloppe->mur_collection as $mur) {
            if (false === $this->supports($mur)) {
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
                    local_non_chauffe_id: $mur->lnc_id($ressource)?->__toString()
                )
            );
        }

        return $collection;
    }
}
