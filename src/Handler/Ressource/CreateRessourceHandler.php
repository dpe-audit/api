<?php

namespace App\Handler\Ressource;

use App\Domain\Adresse\Adresse;
use App\Domain\Batiment\Batiment;
use App\Domain\Ressource\Ressource;
use App\Dto\Ressource\RessourceDto;
use App\Handler\Chauffage\CreateChauffageHandler;
use App\Handler\Ecs\CreateEcsHandler;
use App\Handler\Enveloppe\CreateEnveloppeHandler;
use App\Handler\Logement\CreateLogementHandler;
use App\Handler\Production\CreateProductionHandler;
use App\Handler\Refroidissement\CreateRefroidissementHandler;
use App\Handler\Ventilation\CreateVentilationHandler;

final class CreateRessourceHandler
{
    public function __construct(
        private readonly CreateEnveloppeHandler $createEnveloppeHandler,
        private readonly CreateLogementHandler $createLogementHandler,
        private readonly CreateVentilationHandler $createVentilationHandler,
        private readonly CreateRefroidissementHandler $createRefroidissementHandler,
        private readonly CreateChauffageHandler $createChauffageHandler,
        private readonly CreateEcsHandler $createEcsHandler,
        private readonly CreateProductionHandler $createProductionHandler,
    ) {}

    public function __invoke(RessourceDto $payload): Ressource
    {
        $entity = Ressource::create(
            date_visite: $payload->date_visite,
            date_etablissement: $payload->date_etablissement,
            adresse: Adresse::create(
                nom: $payload->adresse->nom,
                code_postal: $payload->adresse->code_postal,
                code_insee: $payload->adresse->code_insee,
                commune: $payload->adresse->commune,
                ban_id: $payload->adresse->ban_id,
            ),
            batiment: Batiment::create(
                type: $payload->batiment->type,
                annee_construction: $payload->batiment->annee_construction,
                altitude: $payload->batiment->altitude,
                logements: $payload->batiment->logements,
                surface_habitable: $payload->batiment->surface_habitable,
                hauteur_sous_plafond: $payload->batiment->hauteur_sous_plafond,
                materiaux_anciens: $payload->batiment->materiaux_anciens,
                rnb_id: $payload->batiment->rnb_id,
            ),
            enveloppe: $this->createEnveloppeHandler->__invoke($payload->enveloppe),
            ventilation: $this->createVentilationHandler->__invoke($payload->ventilation),
            refroidissement: $this->createRefroidissementHandler->__invoke($payload->refroidissement),
            chauffage: $this->createChauffageHandler->__invoke($payload->chauffage),
            ecs: $this->createEcsHandler->__invoke($payload->ecs),
            production: $this->createProductionHandler->__invoke($payload->production),
        );

        foreach ($payload->logements as $logement) {
            $entity->add_logement($this->createLogementHandler->__invoke($logement, $entity));
        }
        return $entity;
    }
}
