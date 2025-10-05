<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\{XMLPlancherHaut, XMLRessource};
use App\Dto\Enveloppe\Paroi\IsolationDto;
use App\Dto\Enveloppe\PlancherHaut\{PlancherHautDto, PositionDto};

final class PlancherHautTransformer
{
    public function supports(XMLPlancherHaut $element): bool
    {
        return $element->surface() > 0;
    }

    /**
     * @return array<PlancherHautDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->enveloppe->plancher_haut_collection as $plancher_haut) {
            if (false === $this->supports($plancher_haut)) {
                continue;
            }
            $collection[] = new PlancherHautDto(
                id: (string) $plancher_haut->id(),
                description: $plancher_haut->description(),
                configuration: $plancher_haut->configuration(),
                type_structure: $plancher_haut->type_structure(),
                inertie: $plancher_haut->inertie($ressource),
                annee_construction: null,
                annee_renovation: null,
                u0: $plancher_haut->uph0_saisi,
                u: $plancher_haut->uph_saisi,
                isolation: new IsolationDto(
                    etat: $plancher_haut->etat_isolation(),
                    type: $plancher_haut->type_isolation(),
                    annee_installation: $plancher_haut->annee_isolation($ressource),
                    epaisseur: $plancher_haut->epaisseur_isolation(),
                    resistance_thermique: $plancher_haut->resistance_isolation
                ),
                position: new PositionDto(
                    surface: $plancher_haut->surface(),
                    mitoyennete: $plancher_haut->mitoyennete(),
                    orientation: null,
                    local_non_chauffe_id: $plancher_haut->lnc_id($ressource)?->__toString()
                )
            );
        }

        return $collection;
    }
}
