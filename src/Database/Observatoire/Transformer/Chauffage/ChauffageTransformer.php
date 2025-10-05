<?php

namespace App\Database\Observatoire\Transformer\Chauffage;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Chauffage\ChauffageDto;

final class ChauffageTransformer
{
    public function __construct(
        private readonly EmetteurTransformer $emetteurTransformer,
        private readonly GenerateurTransformer $generateurTransformer,
        private readonly InstallationTransformer $installationTransformer,
        private readonly SystemeTransformer $systemeTransformer,
    ) {}

    public function __invoke(XMLRessource $ressource): ChauffageDto
    {
        return new ChauffageDto(
            emetteurs: $this->emetteurTransformer->__invoke($ressource),
            generateurs: $this->generateurTransformer->__invoke($ressource),
            installations: $this->installationTransformer->__invoke($ressource),
            systemes: $this->systemeTransformer->__invoke($ressource),
        );
    }
}
