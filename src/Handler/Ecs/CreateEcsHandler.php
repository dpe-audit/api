<?php

namespace App\Handler\Ecs;

use App\Domain\Ecs\Ecs;
use App\Dto\Ecs\EcsDto;

final class CreateEcsHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(EcsDto $payload): Ecs
    {
        $aggregate = Ecs::create();
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
        foreach ($payload->systemes as $systeme) {
            $aggregate->add_systeme(
                $this->systeme_handler->__invoke(payload: $systeme, aggregate: $aggregate)
            );
        }
        return $aggregate;
    }
}
