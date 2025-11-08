<?php

namespace App\Legacy\Model;

/**
 * @property array<Ventilation> $ventilation_collection
 * @property array<Climatisation> $climatisation_collection
 * @property array<InstallationECS> $installation_ecs_collection
 * @property array<InstallationChauffage> $installation_chauffage_collection
 */
final class Logement
{
    public function __construct(
        public readonly CaracteristiqueGenerale $caracteristique_generale,
        public readonly Meteo $meteo,
        public readonly Enveloppe $enveloppe,
        public readonly array $ventilation_collection,
        public readonly array $climatisation_collection,
        public readonly array $installation_ecs_collection,
        public readonly array $installation_chauffage_collection,
        public readonly ?ProductionElecEnr $production_elec_enr,
        public readonly Sortie $sortie
    ) {}

    /**
     * XPATH //logement
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        $ventilation_collection = [];
        $climatisation_collection = [];
        $installation_chauffage_collection = [];
        $installation_ecs_collection = [];

        foreach ($xml->ventilation_collection->ventilation ?? [] as $item) {
            $ventilation_collection[] = Ventilation::from($item);
        }
        foreach ($xml->climatisation_collection->climatisation ?? [] as $item) {
            $climatisation_collection[] = Climatisation::from($item);
        }
        foreach ($xml->installation_chauffage_collection->installation_chauffage ?? [] as $item) {
            $installation_chauffage_collection[] = InstallationChauffage::from($item);
        }
        foreach ($xml->installation_ecs_collection->installation_ecs ?? [] as $item) {
            $installation_ecs_collection[] = InstallationEcs::from($item);
        }

        return new self(
            caracteristique_generale: CaracteristiqueGenerale::from($xml->caracteristique_generale),
            meteo: Meteo::from($xml->meteo),
            enveloppe: Enveloppe::from($xml->enveloppe),
            ventilation_collection: $ventilation_collection,
            climatisation_collection: $climatisation_collection,
            installation_ecs_collection: $installation_ecs_collection,
            installation_chauffage_collection: $installation_chauffage_collection,
            production_elec_enr: !empty($xml->production_elec_enr) ? ProductionElecEnr::from($xml->production_elec_enr) : null,
            sortie: Sortie::from($xml->sortie)
        );
    }

    public function find_generateur_chauffage(string $reference): ?GenerateurChauffage
    {
        foreach ($this->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                if ($generateur_chauffage->match($reference)) {
                    return $generateur_chauffage;
                }
            }
        }
        return null;
    }

    public function find_generateur_ecs(string $reference): ?GenerateurEcs
    {
        foreach ($this->installation_ecs_collection as $installation_ecs) {
            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                if ($generateur_ecs->match($reference)) {
                    return $generateur_ecs;
                }
            }
        }
        return null;
    }

    public function match_generateur_chauffage(GenerateurEcs $generateur_ecs): ?GenerateurChauffage
    {
        foreach ($this->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                if ($generateur_chauffage->match($generateur_ecs->reference)) {
                    return $generateur_chauffage;
                }
                if ($generateur_chauffage->match_generateur_ecs($generateur_ecs)) {
                    return $generateur_chauffage;
                }
            }
        }
        return null;
    }

    public function match_generateur_ecs(GenerateurChauffage $generateur_chauffage): ?GenerateurEcs
    {
        foreach ($this->installation_ecs_collection as $installation_ecs) {
            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                if ($generateur_ecs->match($generateur_chauffage->reference)) {
                    return $generateur_ecs;
                }
                if ($generateur_ecs->match_generateur_chauffage($generateur_chauffage)) {
                    return $generateur_ecs;
                }
            }
        }
        return null;
    }
}
