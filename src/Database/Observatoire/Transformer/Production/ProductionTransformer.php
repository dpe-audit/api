<?php

namespace App\Database\Observatoire\Transformer\Production;

use App\Database\Observatoire\Model\XMLRessource;
use App\Domain\Common\ValueObject\Id;
use App\Dto\Production\PanneauPhotovoltaiqueDto;
use App\Dto\Production\ProductionDto;

final class ProductionTransformer
{
    public function __invoke(XMLRessource $ressource): ProductionDto
    {
        /** @var PanneauPhotovoltaiqueDto[] */
        $panneaux_photovoltaiques = [];
        $panneaux_pv_collection =  $ressource->logement()->production_elec_enr->panneaux_pv_collection ?? [];

        foreach ($panneaux_pv_collection as $panneaux_pv) {
            if (null === $panneaux_pv->orientation()) {
                continue;
            }
            if (null === $panneaux_pv->inclinaison()) {
                continue;
            }
            if (0 === $panneaux_pv->modules()) {
                continue;
            }
            $panneaux_photovoltaiques[] = new PanneauPhotovoltaiqueDto(
                id: Id::create(),
                description: $panneaux_pv->description(),
                orientation: $panneaux_pv->orientation(),
                inclinaison: $panneaux_pv->inclinaison(),
                modules: $panneaux_pv->modules(),
                surface: $panneaux_pv->surface_totale_capteurs,
                installation_collective: $panneaux_pv->installation_collective(),
            );
        }
        return new ProductionDto(
            panneaux_photovoltaiques: $panneaux_photovoltaiques,
        );
    }
}
