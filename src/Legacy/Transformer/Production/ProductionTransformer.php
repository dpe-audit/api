<?php

namespace App\Legacy\Transformer\Production;

use App\Dto\Production\ProductionDto;
use App\Legacy\Transformer\Context;

final class ProductionTransformer
{
    public function __construct(
        private readonly PanneauPhotovoltaiqueTransformer $panneau_photovoltaique_transformer,
    ) {}

    public function __invoke(Context $context): ProductionDto
    {
        $panneaux_photovoltaiques = [];

        foreach ($context->logement()->production_elec_enr?->panneaux_pv_collection ?? [] as $panneau_pv) {
            $panneaux_photovoltaiques[] = $this->panneau_photovoltaique_transformer->__invoke($panneau_pv, $context);
        }
        return new ProductionDto(
            panneaux_photovoltaiques: array_filter($panneaux_photovoltaiques),
        );
    }
}
