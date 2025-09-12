<?php

namespace App\Api\Chauffage;

use App\Api\Chauffage\Handler\{CreateEmetteurHandler, CreateGenerateurHandler, CreateInstallationHandler, CreateSystemeHandler};
use App\Api\Chauffage\Model\Chauffage as Payload;
use App\Model\Chauffage\Chauffage;

final class CreateChauffageHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateEmetteurHandler $emetteur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(Payload $payload): Chauffage
    {
        $entity = Chauffage::create();

        foreach ($payload->generateurs as $generateur) {
            $entity->add_generateur(
                $this->generateur_handler->__invoke(payload: $generateur, entity: $entity)
            );
        }
        foreach ($payload->emetteurs as $emetteur) {
            $entity->add_emetteur(
                $this->emetteur_handler->__invoke(payload: $emetteur, entity: $entity)
            );
        }
        foreach ($payload->installations as $installation) {
            $entity->add_installation(
                $this->installation_handler->__invoke(payload: $installation, entity: $entity)
            );
        }
        foreach ($payload->systemes as $systeme) {
            $entity->add_systeme(
                $this->systeme_handler->__invoke(payload: $systeme, entity: $entity)
            );
        }
        return $entity;
    }
}
