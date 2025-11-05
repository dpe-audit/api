<?php

namespace App\Legacy\Model;

final class Adresses
{
    public function __construct(
        public readonly Adresse $adresse_bien,
    ) {}

    /**
     * XPATH //administratif/geolocalisation/adresses
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            adresse_bien: Adresse::from($xml->adresse_bien),
        );
    }
}
