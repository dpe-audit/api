<?php

namespace App\Database\Observatoire\Transformer\Refroidissement;

use App\Database\Observatoire\Model\{XMLClimatisation, XMLRessource};
use App\Dto\Refroidissement\InstallationDto;

final class InstallationTransformer
{
    public function supports(XMLClimatisation $element): bool
    {
        return $element->surface_clim > 0;
    }

    /**
     * @return array<InstallationDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_filter(array_map(function (XMLClimatisation $element) {
            if (false === $this->supports($element)) {
                return null;
            }
            return new InstallationDto(
                id: $element->id(),
                description: $element->description(),
                surface: $element->surface_clim,
            );
        }, $ressource->logement()->climatisation_collection));
    }
}
