<?php

namespace App\Database\Observatoire\Transformer\Ventilation;

use App\Database\Observatoire\Model\{XMLRessource, XMLVentilation};
use App\Dto\Ventilation\GenerateurDto;

final class GenerateurTransformer
{
    public function supports(XMLVentilation $element): bool
    {
        return null !== $element->type_generateur();
    }

    /**
     * @return array<GenerateurDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_filter(array_map(function (XMLVentilation $element) use ($ressource) {
            if (false === $this->supports($element)) {
                return null;
            }
            return new GenerateurDto(
                id: (string) $element->id(),
                description: $element->description(),
                type: $element->type_generateur(),
                presence_echangeur_thermique: $element->presence_echangeur_thermique(),
                generateur_collectif: $element->generateur_collectif(),
                annee_installation: $element->annee_installation($ressource),
                type_vmc: $element->type_vmc(),
            );
        }, $ressource->logement()->ventilation_collection));
    }
}
