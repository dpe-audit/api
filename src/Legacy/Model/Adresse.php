<?php

namespace App\Legacy\Model;

final class Adresse
{
    public function __construct(
        public readonly string $adresse_brut,
        public readonly string $code_postal_brut,
        public readonly string $nom_commune_brut,
        public readonly string $label_brut,
        public readonly string $label_brut_avec_complement,
        public readonly int $enum_statut_geocodage_ban_id,
        public readonly \DateTimeImmutable $ban_date_appel,
        public readonly ?string $ban_id,
        public readonly ?string $ban_id_ban_adresse,
        public readonly ?string $ban_label,
        public readonly ?string $ban_housenumber,
        public readonly ?string $ban_street,
        public readonly ?string $ban_citycode,
        public readonly ?string $ban_postcode,
        public readonly ?string $ban_city,
        public readonly ?string $ban_type,
        public readonly ?float $ban_score,
        public readonly ?float $ban_x,
        public readonly ?float $ban_y,
        public readonly ?string $compl_nom_residence,
        public readonly ?string $compl_ref_batiment,
        public readonly ?int $compl_etage_appartement,
        public readonly ?string $compl_ref_cage_escalier,
        public readonly ?string $compl_ref_logement,
    ) {}

    /**
     * XPATH //administratif/geolocalisation/adresses/adresse_bien
     * XPATH //administratif/geolocalisation/adresses/adresse_proprietaire
     * XPATH //administratif/geolocalisation/adresses/adresse_proprietaire_installation_commune
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            adresse_brut: (string) $xml->adresse_brut,
            code_postal_brut: (string) $xml->code_postal_brut,
            nom_commune_brut: (string) $xml->nom_commune_brut,
            label_brut: (string) $xml->label_brut,
            label_brut_avec_complement: (string) $xml->label_brut_avec_complement,
            enum_statut_geocodage_ban_id: (int) $xml->enum_statut_geocodage_ban_id,
            ban_date_appel: new \DateTimeImmutable((string) $xml->ban_date_appel),
            ban_id: (string) $xml->ban_id ?: null,
            ban_id_ban_adresse: (string) $xml->ban_id_ban_adresse ?: null,
            ban_label: (string) $xml->ban_label ?: null,
            ban_housenumber: (string) $xml->ban_housenumber ?: null,
            ban_street: (string) $xml->ban_street ?: null,
            ban_citycode: (string) $xml->ban_citycode ?: null,
            ban_postcode: (string) $xml->ban_postcode ?: null,
            ban_city: (string) $xml->ban_city ?: null,
            ban_type: (string) $xml->ban_type ?: null,
            ban_score: (float) $xml->ban_score ?: null,
            ban_x: (float) $xml->ban_x ?: null,
            ban_y: (float) $xml->ban_y ?: null,
            compl_nom_residence: (string) $xml->compl_nom_residence ?: null,
            compl_ref_batiment: (string) $xml->compl_ref_batiment ?: null,
            compl_etage_appartement: (int) $xml->compl_etage_appartement ?: null,
            compl_ref_cage_escalier: (string) $xml->compl_ref_cage_escalier ?: null,
            compl_ref_logement: (string) $xml->compl_ref_logement ?: null
        );
    }

    public function nom(): string
    {
        return $this->ban_label ?? $this->adresse_brut;
    }

    public function code_postal(): string
    {
        return $this->ban_postcode ?? $this->code_postal_brut;
    }

    public function code_insee(): ?string
    {
        return $this->ban_citycode;
    }

    public function commune(): ?string
    {
        return $this->ban_city ?? $this->nom_commune_brut;
    }
}
