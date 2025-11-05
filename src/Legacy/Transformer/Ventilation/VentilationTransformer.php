<?php

namespace App\Legacy\Transformer\Ventilation;

use App\Dto\Ventilation\VentilationDto;
use App\Legacy\Transformer\Context;

final class VentilationTransformer
{
    public function __construct(
        private readonly GenerateurTransformer $generateur_transformer,
        private readonly InstallationTransformer $installation_transformer,
    ) {}

    public function __invoke(Context $context): VentilationDto
    {
        $generateurs = [];
        $installations = [];

        foreach ($context->logement()->ventilation_collection as $ventilation) {
            $generateurs[] = $this->generateur_transformer->__invoke($ventilation, $context);
            $installations[] = $this->installation_transformer->__invoke($ventilation, $context);
        }
        return new VentilationDto(
            generateurs: array_filter($generateurs),
            installations: array_filter($installations),
        );
    }
}
