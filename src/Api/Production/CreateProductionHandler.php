<?php

namespace App\Api\Production;

use App\Domain\Production\Production;
use App\Dto\Production\ProductionDto;

final class CreateProductionHandler
{
    public function __construct(
        private readonly CreatePanneauPhotovoltaiqueHandler $panneau_photovoltaique_handler,
    ) {}

    public function __invoke(ProductionDto $payload): Production
    {
        $entity = Production::create();

        foreach ($payload->panneaux_photovoltaiques as $panneau_photovoltaique) {
            $entity->add_panneau_photovoltaique(
                $this->panneau_photovoltaique_handler->__invoke(payload: $panneau_photovoltaique, production: $entity)
            );
        }

        return $entity;
    }
}
