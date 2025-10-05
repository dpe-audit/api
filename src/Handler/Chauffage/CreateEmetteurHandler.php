<?php

namespace App\Handler\Chauffage;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Emetteur\Emetteur;
use App\Domain\Common\ValueObject\Id;
use App\Dto\Chauffage\Emetteur\EmetteurDto;

final class CreateEmetteurHandler
{
    public function __invoke(EmetteurDto $payload, Chauffage $aggregate): Emetteur
    {
        return Emetteur::create(
            id: Id::fromString($payload->id),
            chauffage: $aggregate,
            description: $payload->description,
            type: $payload->type,
            temperature_distribution: $payload->temperature_distribution,
            presence_robinet_thermostatique: $payload->presence_robinet_thermostatique,
            annee_installation: $payload->annee_installation,
        );
    }
}
