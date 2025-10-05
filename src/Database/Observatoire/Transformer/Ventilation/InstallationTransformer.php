<?php

namespace App\Database\Observatoire\Transformer\Ventilation;

use App\Database\Observatoire\Model\{XMLRessource, XMLVentilation};
use App\Dto\Ventilation\InstallationDto;

final class InstallationTransformer
{
    public function supports(XMLVentilation $element): bool
    {
        return $element->surface_ventile > 0;
    }

    /**
     * @return array<InstallationDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_filter(array_map(function (XMLVentilation $element) {
            if (false === $this->supports($element)) {
                return null;
            }
            return new InstallationDto(
                id: (string) $element->id(),
                description: $element->description(),
                surface: $element->surface_ventile,
                type: $element->type_ventilation(),
                generateur_id: $element->type_generateur() ? (string) $element->id() : null,
            );
        }, $ressource->logement()->ventilation_collection));
    }
}
