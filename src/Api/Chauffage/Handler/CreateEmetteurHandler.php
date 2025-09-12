<?php

namespace App\Api\Chauffage\Handler;

use App\Api\Chauffage\Model\Emetteur as Payload;
use App\Model\Common\ValueObject\{Annee, Id};
use App\Model\Chauffage\Chauffage;
use App\Model\Chauffage\Entity\Emetteur;

final class CreateEmetteurHandler
{
    public function __invoke(Payload $payload, Chauffage $entity): Emetteur
    {
        return Emetteur::create(
            id: Id::from($payload->id),
            chauffage: $entity,
            description: $payload->description,
            type: $payload->type,
            temperature_distribution: $payload->temperature_distribution,
            presence_robinet_thermostatique: $payload->presence_robinet_thermostatique,
            annee_installation: $payload->annee_installation
                ? Annee::from($payload->annee_installation)
                : null,
        );
    }
}
