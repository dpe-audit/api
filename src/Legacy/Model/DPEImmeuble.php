<?php

namespace App\Legacy\Model;

/**
 * @property array<LogementVisite> $logement_visite_collection
 */
final class DPEImmeuble
{
    public function __construct(
        public readonly array $logement_visite_collection,
    ) {}

    /**
     * XPATH dpe_immeuble
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        $logement_visite_collection = [];

        foreach ($xml->logement_visite_collection->logement_visite ?? [] as $item) {
            $logement_visite_collection[] = LogementVisite::from($item);
        }

        return new self(
            logement_visite_collection: $logement_visite_collection,
        );
    }
}
