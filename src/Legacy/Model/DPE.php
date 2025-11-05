<?php

namespace App\Legacy\Model;

final class DPE implements Ressource
{
    public function __construct(
        public readonly string $id,
        public readonly string $version,
        public readonly Administratif $administratif,
        public readonly Logement $logement,
        public readonly ?DPEImmeuble $dpe_immeuble,
    ) {}

    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            id: (string) $xml->id,
            version: (string) $xml->version,
            administratif: Administratif::from($xml->administratif),
            logement: Logement::from($xml->logement),
            dpe_immeuble: isset($xml->dpe_immeuble) ? DPEImmeuble::from($xml->dpe_immeuble) : null,
        );
    }

    public function administratif(): Administratif
    {
        return $this->administratif;
    }

    public function logement(): Logement
    {
        return $this->logement;
    }

    public function dpe_immeuble(): ?DPEImmeuble
    {
        return $this->dpe_immeuble;
    }
}
