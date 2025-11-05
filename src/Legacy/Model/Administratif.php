<?php

namespace App\Legacy\Model;

final class Administratif
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
        public readonly ?string $enum_modele_dpe_id,
        public readonly ?\DateTimeImmutable $date_visite_diagnostiqueur,
        public readonly ?\DateTimeImmutable $date_visite_auditeur,
        public readonly ?\DateTimeImmutable $date_etablissement_dpe,
        public readonly ?\DateTimeImmutable $date_etablissement_audit,
        public readonly ?\DateTimeImmutable $date_expiration_audit,
        public readonly Geolocalisation $geolocalisation,
    ) {}

    /**
     * XPATH //administratif
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
            enum_modele_dpe_id: (string) $xml->enum_modele_dpe_id ?: null,
            date_visite_diagnostiqueur: ($value = (string) $xml->date_visite_diagnostiqueur) ? new \DateTimeImmutable($value) : null,
            date_visite_auditeur: ($value = (string) $xml->date_visite_auditeur) ? new \DateTimeImmutable($value) : null,
            date_etablissement_dpe: ($value = (string) $xml->date_etablissement_dpe) ? new \DateTimeImmutable($value) : null,
            date_etablissement_audit: ($value = (string) $xml->date_etablissement_audit) ? new \DateTimeImmutable($value) : null,
            date_expiration_audit: ($value = (string) $xml->date_expiration_audit) ? new \DateTimeImmutable($value) : null,
            geolocalisation: Geolocalisation::from($xml->geolocalisation),
        );
    }

    public function date_etablissement(): \DateTimeImmutable
    {
        return $this->date_etablissement_dpe ?? $this->date_etablissement_audit;
    }

    public function date_visite(): \DateTimeImmutable
    {
        return $this->date_visite_diagnostiqueur ?? $this->date_visite_auditeur;
    }

    public function annee_etablissement(): int
    {
        return (int) $this->date_etablissement()->format('Y');
    }
}
