<?php

namespace App\Database\Observatoire\Transformer\Ventilation;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Ventilation\VentilationDto;

final class VentilationTransformer
{
    public function __construct(
        private readonly GenerateurTransformer $generateurTransformer,
        private readonly InstallationTransformer $installationTransformer,
    ) {}

    public function __invoke(XMLRessource $ressource): VentilationDto
    {
        return new VentilationDto(
            generateurs: $this->generateurTransformer->__invoke($ressource),
            installations: $this->installationTransformer->__invoke($ressource),
        );
    }
}
