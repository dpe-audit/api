<?php

namespace App\Database\Observatoire\Transformer\Ecs;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Ecs\EcsDto;

final class EcsTransformer
{
    public function __construct(
        private readonly GenerateurTransformer $generateur_transformer,
        private readonly InstallationTransformer $installation_transformer,
        private readonly SystemeTransformer $systeme_transformer,
    ) {}

    public function __invoke(XMLRessource $ressource): EcsDto
    {
        return new EcsDto(
            generateurs: $this->generateur_transformer->__invoke($ressource),
            installations: $this->installation_transformer->__invoke($ressource),
            systemes: $this->systeme_transformer->__invoke($ressource),
        );
    }
}
