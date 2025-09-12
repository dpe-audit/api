<?php

namespace App\Database\Observatoire\Transformer;

use App\Database\Observatoire\Model\XMLRessource;
use App\Database\Observatoire\Transformer\Adresse\AdresseTransformer;
use App\Database\Observatoire\Transformer\Batiment\BatimentTransformer;
use App\Database\Observatoire\Transformer\Chauffage\ChauffageTransformer;
use App\Database\Observatoire\Transformer\Ecs\EcsTransformer;
use App\Database\Observatoire\Transformer\Enveloppe\EnveloppeTransformer;
use App\Database\Observatoire\Transformer\Logement\LogementTransformer;
use App\Database\Observatoire\Transformer\Production\ProductionTransformer;
use App\Database\Observatoire\Transformer\Refroidissement\RefroidissementTransformer;
use App\Database\Observatoire\Transformer\Ventilation\VentilationTransformer;
use App\Dto\Ressource\RessourceDto;

final class RessourceTransformer
{
    public function __construct(
        private AdresseTransformer $adresse_transformer,
        private BatimentTransformer $batiment_transformer,
        private EnveloppeTransformer $enveloppe_transformer,
        private ChauffageTransformer $chauffage_transformer,
        private EcsTransformer $ecs_transformer,
        private RefroidissementTransformer $refroidissement_transformer,
        private VentilationTransformer $ventilation_transformer,
        private ProductionTransformer $production_transformer,
        private LogementTransformer $logement_transformer,
    ) {}

    public function __invoke(XMLRessource $xml): RessourceDto
    {
        return new RessourceDto(
            id: null,
            date_visite: $xml->administratif->date_visite(),
            date_etablissement: $xml->administratif->date_etablissement(),
            adresse: $this->adresse_transformer->__invoke($xml),
            batiment: $this->batiment_transformer->__invoke($xml),
            enveloppe: $this->enveloppe_transformer->__invoke($xml),
            chauffage: $this->chauffage_transformer->__invoke($xml),
            ecs: $this->ecs_transformer->__invoke($xml),
            refroidissement: $this->refroidissement_transformer->__invoke($xml),
            ventilation: $this->ventilation_transformer->__invoke($xml),
            production: $this->production_transformer->__invoke($xml),
            logements: $this->logement_transformer->__invoke($xml),
        );
    }
}
