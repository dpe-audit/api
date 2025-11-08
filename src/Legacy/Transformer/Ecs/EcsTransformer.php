<?php

namespace App\Legacy\Transformer\Ecs;

use App\Dto\Ecs\EcsDto;
use App\Legacy\Transformer\Context;

final class EcsTransformer
{
    public function __construct(
        private readonly GenerateurTransformer $generateur_transformer,
        private readonly InstallationTransformer $installation_transformer,
        private readonly SystemeTransformer $systeme_transformer,
    ) {}


    public function __invoke(Context $context): EcsDto
    {
        $generateurs = [];
        $installations = [];
        $systemes = [];

        foreach ($context->logement()->installation_ecs_collection as $installation_ecs) {
            $installations[] = $this->installation_transformer->__invoke($installation_ecs, $context);

            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                $generateur = $this->generateur_transformer->__invoke($generateur_ecs, $installation_ecs, $context);
                if ($generateur) {
                    $generateurs[] = $generateur;
                    $systemes[] = $this->systeme_transformer->__invoke($generateur_ecs, $installation_ecs, $context);
                }
            }
        }

        return new EcsDto(
            generateurs: array_filter($generateurs),
            installations: array_filter($installations),
            systemes: array_filter($systemes),
        );
    }
}
