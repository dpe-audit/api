<?php

namespace App\Database\Observatoire\Transformer\Refroidissement;

use App\Database\Observatoire\Model\{XMLClimatisation, XMLRessource};
use App\Dto\Refroidissement\GenerateurDto;

final class GenerateurTransformer
{
    public function supports(XMLClimatisation $element): bool
    {
        return $element->surface_clim > 0;
    }

    /**
     * @return array<GenerateurDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_filter(array_map(function (XMLClimatisation $element) use ($ressource) {
            if (false === $this->supports($element)) {
                return null;
            }
            return new GenerateurDto(
                id: (string) $element->id(),
                reseau_froid_id: null,
                description: $element->description(),
                type: $element->type_generateur(),
                energie: $element->energie_generateur(),
                annee_installation: $element->annee_installation($ressource),
                seer: $element->seer(),
            );
        }, $ressource->logement()->climatisation_collection));
    }
}
