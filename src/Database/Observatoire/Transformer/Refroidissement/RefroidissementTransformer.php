<?php

namespace App\Database\Observatoire\Transformer\Refroidissement;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Refroidissement\RefroidissementDto;

final class RefroidissementTransformer
{
    public function __construct(
        private readonly GenerateurTransformer $generateurTransformer,
        private readonly InstallationTransformer $installationTransformer,
        private readonly SystemeTransformer $systemeTransformer,
    ) {}

    public function __invoke(XMLRessource $ressource): RefroidissementDto
    {
        return new RefroidissementDto(
            generateurs: $this->generateurTransformer->__invoke($ressource),
            installations: $this->installationTransformer->__invoke($ressource),
            systemes: $this->systemeTransformer->__invoke($ressource),
        );
    }
}
