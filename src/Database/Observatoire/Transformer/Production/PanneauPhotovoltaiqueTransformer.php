<?php

namespace App\Database\Observatoire\Transformer\Production;

use App\Database\Observatoire\Model\{XMLPanneauPv, XMLRessource};
use App\Dto\Production\PanneauPhotovoltaiqueDto;

final class PanneauPhotovoltaiqueTransformer
{
    public function supports(XMLPanneauPv $element): bool
    {
        return $element->orientation() !== null
            && $element->inclinaison() !== null
            && $element->modules() > 0;
    }

    /**
     * @return array<PanneauPhotovoltaiqueDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_filter(array_map(function (XMLPanneauPv $element) {
            if (false === $this->supports($element)) {
                return null;
            }
            return new PanneauPhotovoltaiqueDto(
                id: (string) $element->id(),
                description: $element->description(),
                orientation: $element->orientation(),
                inclinaison: $element->inclinaison(),
                modules: $element->modules(),
                surface: $element->surface_totale_capteurs,
                installation_collective: $element->installation_collective(),
            );
        }, $ressource->logement()->production_elec_enr->panneaux_pv_collection ?? []));
    }
}
