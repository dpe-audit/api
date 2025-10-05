<?php

namespace App\Database\Observatoire\Model;

/**
 * @property null|array<XMLLogement> $logement_collection
 * @property null|array<XMLLogementVisite> $logement_visite_collection
 */
final class XMLRessource
{
    use WithId;

    public function __construct(
        public readonly ?string $numero_dpe,
        public readonly ?string $numero_audit,
        public readonly ?string $statut,
        public readonly XMLAdministratif $administratif,
        public readonly ?XMLLogement $logement,
        public readonly ?array $logement_collection,
        public readonly ?array $logement_visite_collection,
    ) {}

    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            numero_dpe: (string) $xml->numero_dpe ?: null,
            numero_audit: (string) $xml->numero_audit ?: null,
            statut: (string) $xml->statut ?: null,
            administratif: XMLAdministratif::from($xml->administratif),
            logement: $xml->logement ? XMLLogement::from($xml->logement) : null,
            logement_collection: $xml->logement_collection ? XMLLogement::from_collection($xml->logement_collection) : null,
            logement_visite_collection: $xml->logement_visite_collection ? XMLLogementVisite::from_collection($xml->logement_visite_collection) : null
        );
    }

    public function logement(): XMLLogement
    {
        if ($this->logement) {
            return $this->logement;
        }
        foreach ($this->logement_collection as $logement) {
            if ($logement->caracteristique_generale->enum_scenario_id === 0) {
                return $logement;
            }
        }
        throw new \UnexpectedValueException('Element /logement manquant');
    }
}
