<?php

namespace App\Legacy\Model;

final class LogementVisite
{
    use WithId;

    public function __construct(
        public readonly string $description,
        public readonly float $surface_habitable_logement,
        public readonly int $enum_position_etage_logement_id,
        public readonly int $enum_typologie_logement_id,
    ) {}

    /**
     * XPATH //logement_visite
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            description: (string) $xml->description,
            surface_habitable_logement: (float) $xml->surface_habitable_logement,
            enum_position_etage_logement_id: (int) $xml->enum_position_etage_logement_id,
            enum_typologie_logement_id: (int) $xml->enum_typologie_logement_id,
        );
    }
}
