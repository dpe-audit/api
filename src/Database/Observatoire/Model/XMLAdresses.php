<?php

namespace App\Database\Observatoire\Model;

final class XMLAdresses
{
    public function __construct(
        public readonly XMLAdresse $adresse_bien,
    ) {}

    /**
     * XSD administratif/geolocalisation/adresses
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            adresse_bien: XMLAdresse::from($xml->adresse_bien),
        );
    }
}
