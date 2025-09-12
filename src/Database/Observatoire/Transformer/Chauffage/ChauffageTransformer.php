<?php

namespace App\Database\Observatoire\Transformer\Chauffage;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Chauffage\ChauffageDto;
use App\Dto\Chauffage\Emetteur\EmetteurDto;
use App\Dto\Chauffage\Installation\{InstallationDto, RegulationDto, SolaireThermiqueDto};

final class ChauffageTransformer
{
    private function transform_generateurs(XMLRessource $ressource): array
    {
        $collection = [];
        $registre = [];

        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                if ($generateur_chauffage->pac_hybride_partie_chaudiere()) {
                    
                }
            }
        }

        return $collection;
    }

    private function transform_emetteurs(XMLRessource $ressource): array
    {
        $collection = [];
        $registre = [];

        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
                if (null === $emetteur_chauffage->type()) {
                    continue;
                }
                if (in_array($emetteur_chauffage->reference, $registre)) {
                    continue;
                }
                $registre[] = $emetteur_chauffage->reference;
                $collection[] = new EmetteurDto(
                    id: $emetteur_chauffage->id(),
                    description: $emetteur_chauffage->description(),
                    type: $emetteur_chauffage->type(),
                    temperature_distribution: $emetteur_chauffage->temperature_distribution(),
                    presence_robinet_thermostatique: $emetteur_chauffage->presence_robinet_thermostatique(),
                    annee_installation: $emetteur_chauffage->annee_installation($ressource),
                );
            }
        }
        return $collection;
    }

    private function transform_installations(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            // Reconstitution des appoints électriques de salle de bains
            foreach ($installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
                if (false === $emetteur_chauffage->appoint_electrique_sdb()) {
                    continue;
                }
                $collection[] = new InstallationDto(
                    id: $emetteur_chauffage->id(),
                    description: $emetteur_chauffage->description(),
                    surface: $emetteur_chauffage->surface_appoint_electrique_sdp(),
                    comptage_individuel: $installation_chauffage->comptage_individuel(),
                    solaire_thermique: new SolaireThermiqueDto(
                        usage: $installation_chauffage->usage_solaire(),
                        annee_installation: null,
                        fch: $installation_chauffage->fch_saisi()
                    ),
                    regulation_centrale: new RegulationDto(
                        presence_regulation: $emetteur_chauffage->presence_regulation_centrale(),
                        minimum_temperature: $emetteur_chauffage->regulation_centrale_minimum_temperature(),
                        detection_presence: $emetteur_chauffage->regulation_centrale_detection_presence(),
                    ),
                    regulation_terminale: new RegulationDto(
                        presence_regulation: $emetteur_chauffage->presence_regulation_terminale(),
                        minimum_temperature: $emetteur_chauffage->regulation_terminale_minimum_temperature(),
                        detection_presence: $emetteur_chauffage->regulation_terminale_detection_presence(),
                    ),
                );
            }
            $collection[] = new InstallationDto(
                id: $installation_chauffage->id(),
                description: $installation_chauffage->description(),
                surface: $installation_chauffage->surface(),
                comptage_individuel: $installation_chauffage->comptage_individuel(),
                solaire_thermique: new SolaireThermiqueDto(
                    usage: $installation_chauffage->usage_solaire(),
                    annee_installation: null,
                    fch: $installation_chauffage->fch_saisi()
                ),
                regulation_centrale: new RegulationDto(
                    presence_regulation: $installation_chauffage->presence_regulation_centrale(),
                    minimum_temperature: $installation_chauffage->regulation_centrale_minimum_temperature(),
                    detection_presence: $installation_chauffage->regulation_centrale_detection_presence(),
                ),
                regulation_terminale: new RegulationDto(
                    presence_regulation: $installation_chauffage->presence_regulation_terminale(),
                    minimum_temperature: $installation_chauffage->regulation_terminale_minimum_temperature(),
                    detection_presence: $installation_chauffage->regulation_terminale_detection_presence(),
                ),
            );
        }
        return $collection;
    }

    private function transform_systemes(XMLRessource $ressource): array
    {
        $collection = [];
        return $collection;
    }

    public function __invoke(XMLRessource $xml): ChauffageDto
    {
        return new ChauffageDto(
            emetteurs: $this->transform_emetteurs($xml),
            generateurs: $this->transform_generateurs($xml),
            installations: $this->transform_installations($xml),
            systemes: $this->transform_systemes($xml),
        );
    }
}
