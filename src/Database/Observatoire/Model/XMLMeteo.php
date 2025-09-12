<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Batiment\ClasseAltitude;

final class XMLMeteo
{
    public function __construct(
        public readonly int $enum_zone_climatique_id,
        public readonly ?float $altitude,
        public readonly int $enum_classe_altitude_id,
        public readonly bool $batiment_materiaux_anciens
    ) {}

    /**
     * XSD /logement/meteo
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            enum_zone_climatique_id: (int) $xml->enum_zone_climatique_id,
            altitude: (float) $xml->altitude ?: null,
            enum_classe_altitude_id: (int) $xml->enum_classe_altitude_id,
            batiment_materiaux_anciens: (bool)(int) $xml->batiment_materiaux_anciens
        );
    }

    public function altitude(): int
    {
        return $this->altitude ?? $this->classe_altitude()->altitude();
    }

    public function classe_altitude(): ClasseAltitude
    {
        return match ($this->enum_classe_altitude_id) {
            1 => ClasseAltitude::_400_LT,
            2 => ClasseAltitude::_400_800,
            3 => ClasseAltitude::_800_GT,
        };
    }
}
