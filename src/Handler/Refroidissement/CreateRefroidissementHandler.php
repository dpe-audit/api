<?php

namespace App\Handler\Refroidissement;

use App\Domain\Refroidissement\Refroidissement;
use App\Dto\Refroidissement\RefroidissementDto;

final class CreateRefroidissementHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(RefroidissementDto $payload): Refroidissement
    {
        $entity = Refroidissement::create();

        foreach ($payload->generateurs as $generateur) {
            $entity->add_generateur(
                $this->generateur_handler->__invoke(payload: $generateur, entity: $entity)
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
