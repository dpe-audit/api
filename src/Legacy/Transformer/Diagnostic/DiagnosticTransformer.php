<?php

namespace App\Legacy\Transformer\Diagnostic;

use App\Domain\Common\ValueObject\Id;
use App\Dto\Diagnostic\DiagnosticDto;
use App\Legacy\Model\DPE;
use App\Legacy\Transformer\Batiment\BatimentTransformer;
use App\Legacy\Transformer\Chauffage\ChauffageTransformer;
use App\Legacy\Transformer\Context;
use App\Legacy\Transformer\Ecs\EcsTransformer;
use App\Legacy\Transformer\Enveloppe\EnveloppeTransformer;
use App\Legacy\Transformer\Logement\LogementTransformer;
use App\Legacy\Transformer\Production\ProductionTransformer;
use App\Legacy\Transformer\Refroidissement\RefroidissementTransformer;
use App\Legacy\Transformer\Ventilation\VentilationTransformer;

final class DiagnosticTransformer
{
    public function __construct(
        private readonly BatimentTransformer $batiment_transformer,
        private readonly LogementTransformer $logement_transformer,
        private readonly EnveloppeTransformer $enveloppe_transformer,
        private readonly ChauffageTransformer $chauffage_transformer,
        private readonly EcsTransformer $ecs_transformer,
        private readonly VentilationTransformer $ventilation_transformer,
        private readonly RefroidissementTransformer $refroidissement_transformer,
        private readonly ProductionTransformer $production_transformer,
    ) {}

    public function __invoke(DPE $data): DiagnosticDto
    {
        $context = new Context(ressource: $data, logement: $data->logement());

        $logements = [];
        foreach ($context->ressource()->dpe_immeuble()?->logement_visite_collection ?? [] as $logement_visite) {
            $logements[] = $this->logement_transformer->__invoke($logement_visite, $context);
        }

        return new DiagnosticDto(
            id: (string) Id::create(),
            date_visite: $data->administratif()->date_visite(),
            date_etablissement: $data->administratif()->date_etablissement(),
            batiment: $this->batiment_transformer->__invoke($context),
            enveloppe: $this->enveloppe_transformer->__invoke($context),
            chauffage: $this->chauffage_transformer->__invoke($context),
            ecs: $this->ecs_transformer->__invoke($context),
            ventilation: $this->ventilation_transformer->__invoke($context),
            refroidissement: $this->refroidissement_transformer->__invoke($context),
            production: $this->production_transformer->__invoke($context),
            logements: array_filter($logements),
        );
    }
}
