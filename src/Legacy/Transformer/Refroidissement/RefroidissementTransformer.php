<?php

namespace App\Legacy\Transformer\Refroidissement;

use App\Dto\Refroidissement\RefroidissementDto;
use App\Legacy\Transformer\Context;

final class RefroidissementTransformer
{
    public function __construct(
        private readonly GenerateurTransformer $generateur_transformer,
        private readonly InstallationTransformer $installation_transformer,
        private readonly SystemeTransformer $systeme_transformer,
    ) {}

    public function __invoke(Context $context): RefroidissementDto
    {
        $generateurs = [];
        $installations = [];
        $systemes = [];

        foreach ($context->logement()->climatisation_collection as $climatisation) {
            $generateurs[] = $this->generateur_transformer->__invoke($climatisation, $context);
            $installations[] = $this->installation_transformer->__invoke($climatisation, $context);
            $systemes[] = $this->systeme_transformer->__invoke($climatisation, $context);
        }
        return new RefroidissementDto(
            generateurs: array_filter($generateurs),
            installations: array_filter($installations),
            systemes: array_filter($systemes),
        );
    }
}
