<?php

namespace App\Api\Chauffage\Handler;

use App\Api\Chauffage\Model\Installation as Payload;
use App\Model\Common\ValueObject\{Annee, Id, Pourcentage};
use App\Model\Chauffage\Chauffage;
use App\Model\Chauffage\Entity\Installation;
use App\Model\Chauffage\ValueObject\{Regulation, Solaire};

final class CreateInstallationHandler
{
    public function __invoke(Payload $payload, Chauffage $entity): Installation
    {
        return Installation::create(
            id: Id::from($payload->id),
            chauffage: $entity,
            description: $payload->description,
            surface: $payload->surface,
            comptage_individuel: $payload->comptage_individuel,
            solaire_thermique: $payload->solaire_thermique ? Solaire::create(
                usage: $payload->solaire_thermique->usage,
                annee_installation: $payload->solaire_thermique->annee_installation
                    ? Annee::from($payload->solaire_thermique->annee_installation)
                    : null,
                fch: $payload->solaire_thermique->fch
                    ? Pourcentage::from($payload->solaire_thermique->fch)
                    : null,
            ) : null,
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
        );
    }
}
