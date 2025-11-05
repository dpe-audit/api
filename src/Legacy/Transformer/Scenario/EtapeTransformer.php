<?php

namespace App\Legacy\Transformer\Scenario;

use App\Domain\Common\ValueObject\Id;
use App\Dto\Scenario\EtapeDto;
use App\Legacy\Transformer\Batiment\BatimentTransformer;
use App\Legacy\Transformer\Chauffage\ChauffageTransformer;
use App\Legacy\Transformer\Context;
use App\Legacy\Transformer\Ecs\EcsTransformer;
use App\Legacy\Transformer\Enveloppe\EnveloppeTransformer;
use App\Legacy\Transformer\Production\ProductionTransformer;
use App\Legacy\Transformer\Refroidissement\RefroidissementTransformer;
use App\Legacy\Transformer\Ventilation\VentilationTransformer;

final class EtapeTransformer
{
    private Context $context;

    public function __construct(
        private readonly BatimentTransformer $batiment_transformer,
        private readonly EnveloppeTransformer $enveloppe_transformer,
        private readonly ChauffageTransformer $chauffage_transformer,
        private readonly EcsTransformer $ecs_transformer,
        private readonly VentilationTransformer $ventilation_transformer,
        private readonly RefroidissementTransformer $refroidissement_transformer,
        private readonly ProductionTransformer $production_transformer,
    ) {}

    public function description(): string
    {
        return match ($this->context->logement()->caracteristique_generale->enum_etape_id) {
            1 => "Première étape",
            2 => "Dernière étape",
            3 => "Étape intermédiaire n°1",
            4 => "Étape intermédiaire n°2",
            5 => "Étape intermédiaire n°3",
            default => "étape inconnue",
        };
    }

    public function __invoke(Context $context): ?EtapeDto
    {
        $this->context = $context;

        if (null === $context->logement()) {
            return null;
        }
        if (null === $context->logement()->caracteristique_generale->enum_etape_id) {
            return null;
        }
        if (0 === $context->logement()->caracteristique_generale->enum_etape_id) {
            return null;
        }

        return new EtapeDto(
            id: (string) Id::create(),
            nom: "Etape",
            description: $this->description(),
            enveloppe: $this->enveloppe_transformer->__invoke($context),
            chauffage: $this->chauffage_transformer->__invoke($context),
            ecs: $this->ecs_transformer->__invoke($context),
            ventilation: $this->ventilation_transformer->__invoke($context),
            refroidissement: $this->refroidissement_transformer->__invoke($context),
            production: $this->production_transformer->__invoke($context),
        );
    }
}
