<?php

namespace App\Handler\Production;

use App\Domain\Production\Production;
use App\Dto\Production\ProductionDto;

final class CreateProductionHandler
{
    public function __construct(
        private readonly CreatePanneauPhotovoltaiqueHandler $panneau_photovoltaique_handler,
    ) {}

    public function __invoke(ProductionDto $payload): Production
    {
        $aggregate = Production::create();

        foreach ($payload->panneaux_photovoltaiques as $panneau_photovoltaique) {
            $aggregate->add_panneau_photovoltaique(
                $this->panneau_photovoltaique_handler->__invoke(payload: $panneau_photovoltaique, aggregate: $aggregate)
            );
        }

        return $aggregate;
    }
}
