<?php

namespace App\Legacy\Transformer\Audit;

use App\Domain\Common\ValueObject\Id;
use App\Dto\Audit\AuditDto;
use App\Legacy\Model\Audit;
use App\Legacy\Transformer\Batiment\BatimentTransformer;
use App\Legacy\Transformer\Context;
use App\Legacy\Transformer\Logement\LogementTransformer;
use App\Legacy\Transformer\Scenario\EtapeTransformer;

final class AuditTransformer
{
    private Context $context;

    public function __construct(
        private readonly BatimentTransformer $batiment_transformer,
        private readonly LogementTransformer $logement_transformer,
        private readonly EtapeTransformer $etape_transformer,
    ) {}

    public function __invoke(Audit $data): AuditDto
    {
        $context = new Context(ressource: $data);
        $id = (string) Id::create();

        $logements = [];
        foreach ($context->ressource()->dpe_immeuble()?->logement_visite_collection ?? [] as $logement_visite) {
            $logements[] = $this->logement_transformer->__invoke($logement_visite, $context);
        }


        return new AuditDto(
            id: $id,
            diagnostic_id: $id,
            date_visite: $data->administratif()->date_visite(),
            date_etablissement: $data->administratif()->date_etablissement(),
            batiment: $this->batiment_transformer->__invoke($context),
            logements: array_filter($logements),
            scenarios: [],
        );
    }
}
