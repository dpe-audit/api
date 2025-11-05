<?php

namespace App\Legacy\Model;

final class Geolocalisation
{
    public function __construct(
        public readonly ?string $invar_logement,
        public readonly ?string $numero_fiscal_local,
        public readonly ?string $id_batiment_rnb,
        public readonly ?string $rpls_log_id,
        public readonly ?string $rpls_org_id,
        public readonly ?string $idpar,
        public readonly ?string $immatriculation_copropriete,
        public readonly Adresses $adresses,
    ) {}

    /**
     * XPATH //administratif/geolocalisation
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            invar_logement: (string) $xml->invar_logement,
            numero_fiscal_local: (string) $xml->numero_fiscal_local,
            id_batiment_rnb: (string) $xml->id_batiment_rnb,
            rpls_log_id: (string) $xml->rpls_log_id,
            rpls_org_id: (string) $xml->rpls_org_id,
            idpar: (string) $xml->idpar,
            immatriculation_copropriete: (string) $xml->immatriculation_copropriete,
            adresses: Adresses::from($xml->adresses)
        );
    }
}
