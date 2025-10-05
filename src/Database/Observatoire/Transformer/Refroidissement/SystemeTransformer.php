<?php

namespace App\Database\Observatoire\Transformer\Refroidissement;

use App\Database\Observatoire\Model\{XMLClimatisation, XMLRessource};
use App\Dto\Refroidissement\SystemeDto;

final class SystemeTransformer
{
    public function supports(XMLClimatisation $element): bool
    {
        return $element->surface_clim > 0;
    }

    /**
     * @return array<SystemeDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_filter(array_map(function (XMLClimatisation $element) {
            if (false === $this->supports($element)) {
                return null;
            }
            return new SystemeDto(
                id: (string) $element->id(),
                description: $element->description(),
                installation_id: $element->id(),
                generateur_id: (string) $element->id(),
            );
        }, $ressource->logement()->climatisation_collection));
    }
}
