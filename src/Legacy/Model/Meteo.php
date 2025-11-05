<?php

namespace App\Legacy\Model;

final class Meteo
{
    public function __construct(
        public readonly bool $batiment_materiaux_anciens,
        public readonly ?float $altitude,
        public readonly int $enum_zone_climatique_id,
        public readonly int $enum_classe_altitude_id,
    ) {}

    /**
     * XPATH //meteo
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            batiment_materiaux_anciens: (bool)(int) $xml->batiment_materiaux_anciens,
            altitude: (float) $xml->altitude ?: null,
            enum_zone_climatique_id: (int) $xml->enum_zone_climatique_id,
            enum_classe_altitude_id: (int) $xml->enum_classe_altitude_id,
        );
    }
}
