<?php

namespace App\Database\Observatoire\Model;

/**
 * @property array<XMLVentilation> $ventilation_collection
 * @property array<XMLClimatisation> $climatisation_collection
 * @property array<XMLInstallationECS> $installation_ecs_collection
 * @property array<XMLInstallationChauffage> $installation_chauffage_collection
 */
final class XMLLogement
{
    public function __construct(
        public readonly XMLCaracteristiqueGenerale $caracteristique_generale,
        public readonly XMLMeteo $meteo,
        public readonly XMLEnveloppe $enveloppe,
        public readonly array $ventilation_collection,
        public readonly array $climatisation_collection,
        public readonly array $installation_ecs_collection,
        public readonly array $installation_chauffage_collection,
        public readonly ?XMLProductionElecEnr $production_elec_enr,
        public readonly XMLSortie $sortie
    ) {}

    /**
     * XSD /logement
     * XSD /logement_collection/logement
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            caracteristique_generale: XMLCaracteristiqueGenerale::from($xml->caracteristique_generale),
            meteo: XMLMeteo::from($xml->meteo),
            enveloppe: XMLEnveloppe::from($xml->enveloppe),
            ventilation_collection: XMLVentilation::from_collection($xml->ventilation_collection),
            climatisation_collection: XMLClimatisation::from_collection($xml->climatisation_collection),
            installation_ecs_collection: XMLInstallationECS::from_collection($xml->installation_ecs_collection),
            installation_chauffage_collection: XMLInstallationChauffage::from_collection($xml->installation_chauffage_collection),
            production_elec_enr: $xml->production_elec_enr ? XMLProductionElecEnr::from($xml->production_elec_enr) : null,
            sortie: XMLSortie::from($xml->sortie)
        );
    }

    /**
     * XSD /logement_collection
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->logement as $item) {
            $collection[] = self::from($item);
        }

        return $collection;
    }
}
