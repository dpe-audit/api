<?php

namespace App\Database\Observatoire\Model;

final class XMLAdministratif
{
    public function __construct(
        public readonly ?string $numero_dpe,
        public readonly ?string $dpe_a_remplacer,
        public readonly ?string $audit_a_remplacer,
        public readonly ?string $dpe_immeuble_associe,
        public readonly ?string $enum_version_id,
        public readonly ?string $enum_version_dpe_id,
        public readonly ?string $enum_version_audit_id,
        public readonly ?string $enum_modele_audit_id,
        public readonly ?string $date_visite_diagnostiqueur,
        public readonly ?string $date_visite_auditeur,
        public readonly ?string $date_etablissement_dpe,
        public readonly ?string $date_etablissement_audit,
        public readonly ?string $enum_modele_dpe_id,
        public readonly XMLGeolocalisation $geolocalisation,
    ) {}

    /**
     * XSD audit/administratif
     * XSD dpe/administratif
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            numero_dpe: (string) $xml->numero_dpe ?: null,
            dpe_a_remplacer: (string) $xml->dpe_a_remplacer ?: null,
            audit_a_remplacer: (string) $xml->audit_a_remplacer ?: null,
            dpe_immeuble_associe: (string) $xml->dpe_immeuble_associe ?: null,
            enum_version_id: (string) $xml->enum_version_id ?: null,
            enum_version_dpe_id: (string) $xml->enum_version_dpe_id ?: null,
            enum_version_audit_id: (string) $xml->enum_version_audit_id ?: null,
            enum_modele_audit_id: (string) $xml->enum_modele_audit_id ?: null,
            date_visite_diagnostiqueur: (string) $xml->date_visite_diagnostiqueur ?: null,
            date_visite_auditeur: (string) $xml->date_visite_auditeur ?: null,
            date_etablissement_dpe: (string) $xml->date_etablissement_dpe ?: null,
            date_etablissement_audit: (string) $xml->date_etablissement_audit ?: null,
            enum_modele_dpe_id: (string) $xml->enum_modele_dpe_id ?: null,
            geolocalisation: XMLGeolocalisation::from($xml->geolocalisation)
        );
    }

    public function date_etablissement(): \DateTimeImmutable
    {
        try {
            $date = $this->date_etablissement_dpe ?? $this->date_etablissement_audit;
            return new \DateTimeImmutable($date);
        } catch (\Throwable $th) {
            return new \DateTimeImmutable();
        }
    }

    public function date_visite(): \DateTimeImmutable
    {
        try {
            $date = $this->date_visite_diagnostiqueur ?? $this->date_visite_auditeur;
            return new \DateTimeImmutable($date);
        } catch (\Throwable $th) {
            return new \DateTimeImmutable();
        }
    }

    public function annee_etablissement(): int
    {
        return (int) $this->date_etablissement()->format('Y');
    }
}
