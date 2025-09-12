<?php

namespace App\Api\Ecs\Handler;

use App\Api\Ecs\Model\Installation as Payload;
use App\Model\Common\ValueObject\{Annee, Id, Pourcentage};
use App\Model\Ecs\Ecs;
use App\Model\Ecs\Entity\Installation;
use App\Model\Ecs\ValueObject\Solaire;

final class CreateInstallationHandler
{
    public function __invoke(Payload $payload, Ecs $entity): Installation
    {
        return Installation::create(
            id: Id::from($payload->id),
            ecs: $entity,
            description: $payload->description,
            surface: $payload->surface,
            solaire_thermique: $payload->solaire_thermique ? Solaire::create(
                usage: $payload->solaire_thermique->usage,
                annee_installation: $payload->solaire_thermique->annee_installation
                    ? Annee::from($payload->solaire_thermique->annee_installation)
                    : null,
                fecs: $payload->solaire_thermique->fecs
                    ? Pourcentage::from($payload->solaire_thermique->fecs)
                    : null,
            ) : null,
        );
    }
}
