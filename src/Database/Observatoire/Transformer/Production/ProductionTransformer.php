<?php

namespace App\Database\Observatoire\Transformer\Production;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Production\ProductionDto;

final class ProductionTransformer
{
    public function __construct(
        private readonly PanneauPhotovoltaiqueTransformer $panneauPhotovoltaiqueTransformer,
    ) {}

    public function __invoke(XMLRessource $ressource): ProductionDto
    {
        return new ProductionDto(
            panneaux_photovoltaiques: $this->panneauPhotovoltaiqueTransformer->__invoke($ressource),
        );
    }
}
