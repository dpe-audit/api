<?php

namespace App\Api\Ventilation;

use App\Domain\Ventilation\Ventilation;
use App\Dto\Ventilation\VentilationDto;

final class CreateVentilationHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateInstallationHandler $installation_handler,
    ) {}

    public function __invoke(VentilationDto $payload): Ventilation
    {
        $entity = Ventilation::create();

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
        return $entity;
    }
}
