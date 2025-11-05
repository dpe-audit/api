<?php

namespace App\Legacy\Model;

/**
 * @property array<Logement> $logement_collection
 */
final class Audit implements Ressource
{
    public function __construct(
        public readonly string $id,
        public readonly string $version,
        public readonly Administratif $administratif,
        public readonly array $logement_collection,
        public readonly ?DPEImmeuble $dpe_immeuble,
    ) {}

    public static function from(\SimpleXMLElement $xml): self
    {
        $logement_collection = [];

        foreach ($xml->logement_collection->logement ?? [] as $item) {
            $logement_collection[] = Logement::from($item);
        }
        return new self(
            id: (string) $xml->id,
            version: (string) $xml->version,
            administratif: Administratif::from($xml->administratif),
            logement_collection: $logement_collection,
            dpe_immeuble: isset($xml->dpe_immeuble) ? DPEImmeuble::from($xml->dpe_immeuble) : null,
        );
    }

    public function administratif(): Administratif
    {
        return $this->administratif;
    }

    public function logement(): Logement
    {
        return array_find($this->logement_collection, fn($item) => $item->caracteristique_generale->enum_scenario_id === 0);
    }

    public function dpe_immeuble(): ?DPEImmeuble
    {
        return $this->dpe_immeuble;
    }
}
