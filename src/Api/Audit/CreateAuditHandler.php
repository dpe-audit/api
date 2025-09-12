<?php

namespace App\Api\Audit;

use App\Api\Audit\Model\Audit as Payload;
use App\Api\Chauffage\CreateChauffageHandler;
use App\Api\Ecs\CreateEcsHandler;
use App\Api\Enveloppe\CreateEnveloppeHandler;
use App\Api\Production\CreateProductionHandler;
use App\Api\Refroidissement\CreateRefroidissementHandler;
use App\Api\Ventilation\CreateVentilationHandler;
use App\Model\Audit\Audit;
use App\Model\Audit\ValueObject\{Adresse, Batiment};
use App\Model\Common\ValueObject\Annee;
use App\Model\Common\ValueObject\Id;

final class CreateAuditHandler
{
    public function __construct(
        private readonly ComputeAuditHandler $compute_handler,
        private readonly CreateEnveloppeHandler $enveloppe_handler,
        private readonly CreateVentilationHandler $ventilation_handler,
        private readonly CreateChauffageHandler $chauffage_handler,
        private readonly CreateEcsHandler $ecs_handler,
        private readonly CreateRefroidissementHandler $refroidissement_handler,
        private readonly CreateProductionHandler $production_handler,
    ) {}

    public function __invoke(Payload $payload): Audit
    {
        $handle_enveloppe = $this->enveloppe_handler;
        $handle_ventilation = $this->ventilation_handler;
        $handle_chauffage = $this->chauffage_handler;
        $handle_ecs = $this->ecs_handler;
        $handle_refroidissement = $this->refroidissement_handler;
        $handle_production = $this->production_handler;

        $entity = Audit::create(
            adresse: Adresse::create(
                numero: $payload->adresse->numero,
                nom: $payload->adresse->nom,
                code_postal: $payload->adresse->code_postal,
                code_commune: $payload->adresse->code_commune,
                commune: $payload->adresse->commune,
                ban_id: $payload->adresse->ban_id,
            ),
            batiment: Batiment::create(
                annee_construction: Annee::from($payload->batiment->annee_construction),
                altitude: $payload->batiment->altitude,
                logements: $payload->batiment->logements,
                surface_habitable: $payload->batiment->surface_habitable,
                hauteur_sous_plafond: $payload->batiment->hauteur_sous_plafond,
                materiaux_anciens: $payload->batiment->materiaux_anciens,
                rnb_id: $payload->batiment->rnb_id ? Id::from($payload->batiment->rnb_id) : null,
            ),
            enveloppe: $handle_enveloppe($payload->enveloppe),
            ventilation: $handle_ventilation($payload->ventilation),
            chauffage: $handle_chauffage($payload->chauffage),
            ecs: $handle_ecs($payload->ecs),
            refroidissement: $handle_refroidissement($payload->refroidissement),
            production: $handle_production($payload->production),
        );

        $this->compute_handler->__invoke($entity);

        return $entity;
    }
}
