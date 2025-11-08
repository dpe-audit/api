<?php

namespace App\Handler\Ventilation;

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
        $aggregate = Ventilation::create();

        foreach ($payload->generateurs as $generateur) {
            $aggregate->add_generateur(
                $this->generateur_handler->__invoke(payload: $generateur, aggregate: $aggregate)
            );
        }
        foreach ($payload->installations as $installation) {
            $aggregate->add_installation(
                $this->installation_handler->__invoke(payload: $installation, aggregate: $aggregate)
            );
        }
        return $aggregate;
    }
}
