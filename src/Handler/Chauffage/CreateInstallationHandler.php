<?php

namespace App\Handler\Chauffage;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\Installation\Installation;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Installation\Regulation\Regulation;
use App\Domain\Chauffage\Installation\Solaire\Solaire;
use App\Dto\Chauffage\Installation\InstallationDto;

final class CreateInstallationHandler
{
    public function __invoke(InstallationDto $payload, Chauffage $aggregate): Installation
    {
        return Installation::create(
            id: Id::fromString($payload->id),
            chauffage: $aggregate,
            description: $payload->description,
            surface: $payload->surface,
            comptage_individuel: $payload->comptage_individuel,
            regulation_centrale: Regulation::create(
                presence_regulation: $payload->regulation_centrale->presence_regulation,
                minimum_temperature: $payload->regulation_centrale->minimum_temperature,
                detection_presence: $payload->regulation_centrale->detection_presence,
            ),
            regulation_terminale: Regulation::create(
                presence_regulation: $payload->regulation_terminale->presence_regulation,
                minimum_temperature: $payload->regulation_terminale->minimum_temperature,
                detection_presence: $payload->regulation_terminale->detection_presence,
            ),
            solaire_thermique: $payload->solaire_thermique ? Solaire::create(
                usage: $payload->solaire_thermique->usage,
                annee_installation: $payload->solaire_thermique->annee_installation,
                fch: $payload->solaire_thermique->fch,
            ) : null,
        );
    }
}
