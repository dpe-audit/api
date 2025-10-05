<?php

namespace App\Handler\Chauffage;

use App\Domain\Chauffage\Chauffage;
use App\Dto\Chauffage\ChauffageDto;

final class CreateChauffageHandler
{
    public function __construct(
        private readonly CreateEmetteurHandler $emetteur_handler,
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(ChauffageDto $payload): Chauffage
    {
        $entity = Chauffage::create();
        foreach ($payload->emetteurs as $emetteur) {
            $entity->add_emetteur(
                $this->emetteur_handler->__invoke(payload: $emetteur, aggregate: $entity)
            );
        }
        foreach ($payload->generateurs as $generateur) {
            $entity->add_generateur(
                $this->generateur_handler->__invoke(payload: $generateur, aggregate: $entity)
            );
        }
        foreach ($payload->installations as $installation) {
            $entity->add_installation(
                $this->installation_handler->__invoke(payload: $installation, aggregate: $entity)
            );
        }
        foreach ($payload->systemes as $systeme) {
            $entity->add_systeme(
                $this->systeme_handler->__invoke(payload: $systeme, aggregate: $entity)
            );
        }
        return $entity;
    }
}
