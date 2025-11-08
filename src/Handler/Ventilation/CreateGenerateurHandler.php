<?php

namespace App\Handler\Ventilation;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Generateur\Generateur;
use App\Domain\Ventilation\Ventilation;
use App\Dto\Ventilation\GenerateurDto;

final class CreateGenerateurHandler
{
    public function __invoke(GenerateurDto $payload, Ventilation $aggregate): Generateur
    {
        return Generateur::create(
            id: Id::fromString($payload->id),
            ventilation: $aggregate,
            description: $payload->description,
            type: $payload->type,
            presence_echangeur_thermique: $payload->presence_echangeur_thermique,
            generateur_collectif: $payload->generateur_collectif,
            type_vmc: $payload->type_vmc,
            annee_installation: $payload->annee_installation,
        );
    }
}
