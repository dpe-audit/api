<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Logement\Position;
use App\Domain\Logement\Typologie;

final class XMLLogementVisite
{
    use WithId;

    public function __construct(
        public readonly string $description,
        public readonly int $enum_position_etage_logement_id,
        public readonly int $enum_typologie_logement_id,
        public readonly float $surface_habitable_logement,
    ) {}

    /**
     * XSD /dpe_immeuble/logement_visite_collection/logement_visite
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            description: (string) $xml->description,
            enum_position_etage_logement_id: (int) $xml->enum_position_etage_logement_id,
            enum_typologie_logement_id: (int) $xml->enum_typologie_logement_id,
            surface_habitable_logement: (float) $xml->surface_habitable_logement,
        );
    }

    /**
     * XSD /dpe_immeuble/logement_visite_collection
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->logement as $item) {
            $collection[] = self::from($item);
        }

        return $collection;
    }

    public function position(): Position
    {
        return match ($this->enum_position_etage_logement_id) {
            1 => Position::RDC,
            2 => Position::ETAGE_INTERMEDIAIRE,
            3 => Position::DERNIER_ETAGE,
        };
    }

    public function typologie(): Typologie
    {
        return match ($this->enum_typologie_logement_id) {
            1 => Typologie::T1,
            2 => Typologie::T2,
            3 => Typologie::T3,
            4 => Typologie::T4,
            5 => Typologie::T5,
            6 => Typologie::T6,
            7 => Typologie::T7,
        };
    }
}
