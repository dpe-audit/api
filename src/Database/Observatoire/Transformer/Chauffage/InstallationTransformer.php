<?php

namespace App\Database\Observatoire\Transformer\Chauffage;

use App\Database\Observatoire\Model\{XMLInstallationChauffage, XMLRessource};
use App\Dto\Chauffage\Installation\{InstallationDto, RegulationDto, SolaireThermiqueDto};

final class InstallationTransformer
{
    public function supports(XMLInstallationChauffage $element): bool
    {
        return $element->surface() > 0;
    }

    /**
     * @return array<InstallationDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            if (false === $this->supports($installation_chauffage)) {
                continue;
            }
            $collection[] = new InstallationDto(
                id: (string) $installation_chauffage->id(),
                description: $installation_chauffage->description(),
                surface: $installation_chauffage->surface(false),
                comptage_individuel: $installation_chauffage->comptage_individuel(false),
                solaire_thermique: $installation_chauffage->usage_solaire() ? new SolaireThermiqueDto(
                    usage: $installation_chauffage->usage_solaire(),
                    annee_installation: null,
                    fch: $installation_chauffage->fch_saisi(),
                ) : null,
                regulation_centrale: new RegulationDto(
                    presence_regulation: $installation_chauffage->presence_regulation_centrale(false),
                    minimum_temperature: $installation_chauffage->regulation_centrale_minimum_temperature(false),
                    detection_presence: $installation_chauffage->regulation_centrale_detection_presence(false),
                ),
                regulation_terminale: new RegulationDto(
                    presence_regulation: $installation_chauffage->presence_regulation_terminale(false),
                    minimum_temperature: $installation_chauffage->regulation_terminale_minimum_temperature(false),
                    detection_presence: $installation_chauffage->regulation_terminale_detection_presence(false),
                ),
            );
            if ($installation_chauffage->appoint_electrique_sdb()) {
                $collection[] = new InstallationDto(
                    id: (string) $installation_chauffage->id_installation_sdb(),
                    description: $installation_chauffage->description(),
                    surface: $installation_chauffage->surface(true),
                    comptage_individuel: $installation_chauffage->comptage_individuel(true),
                    solaire_thermique: null,
                    regulation_centrale: new RegulationDto(
                        presence_regulation: $installation_chauffage->presence_regulation_centrale(true),
                        minimum_temperature: $installation_chauffage->regulation_centrale_minimum_temperature(true),
                        detection_presence: $installation_chauffage->regulation_centrale_detection_presence(true),
                    ),
                    regulation_terminale: new RegulationDto(
                        presence_regulation: $installation_chauffage->presence_regulation_terminale(true),
                        minimum_temperature: $installation_chauffage->regulation_terminale_minimum_temperature(true),
                        detection_presence: $installation_chauffage->regulation_terminale_detection_presence(true),
                    ),
                );
            }
        }
        return $collection;
    }
}
