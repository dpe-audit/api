<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class Climatisation
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_clim,
        public readonly ?int $nombre_logement_echantillon,
        public readonly ?float $cle_repartition_clim,
        public readonly ?string $ref_produit_fr,

        public readonly int $enum_periode_installation_fr_id,
        public readonly int $enum_methode_saisie_carac_sys_id,
        public readonly int $enum_methode_calcul_conso_id,
        public readonly int $enum_type_generateur_fr_id,
        public readonly ?int $enum_type_energie_id,
        public readonly ?int $tv_seer_id,

        public readonly float $eer,
        public readonly float $besoin_fr,
        public readonly float $conso_fr,
        public readonly float $conso_fr_depensier
    ) {}

    /**
     * XPATH //climatisation
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            surface_clim: (float) $xml->donnee_entree->surface_clim,
            nombre_logement_echantillon: (int) $xml->donnee_entree->nombre_logement_echantillon ?: null,
            ref_produit_fr: (string) $xml->donnee_entree->ref_produit_fr ?: null,
            cle_repartition_clim: (float) $xml->donnee_entree->cle_repartition_clim ?: null,

            enum_methode_calcul_conso_id: (int) $xml->donnee_entree->enum_methode_calcul_conso_id,
            enum_type_generateur_fr_id: (int) $xml->donnee_entree->enum_type_generateur_fr_id,
            enum_periode_installation_fr_id: (int) $xml->donnee_entree->enum_periode_installation_fr_id,
            enum_methode_saisie_carac_sys_id: (int) $xml->donnee_entree->enum_methode_saisie_carac_sys_id,
            enum_type_energie_id: (int) $xml->donnee_entree->enum_type_energie_id ?: null,
            tv_seer_id: (int) $xml->donnee_entree->tv_seer_id ?: null,

            eer: (float) $xml->donnee_intermediaire->eer,
            besoin_fr: (float) $xml->donnee_intermediaire->besoin_fr,
            conso_fr: (float) $xml->donnee_intermediaire->conso_fr,
            conso_fr_depensier: (float) $xml->donnee_intermediaire->conso_fr_depensier
        );
    }

    public function eer_saisi(): ?float
    {
        return in_array($this->enum_methode_saisie_carac_sys_id, [6, 8]) ? $this->eer : null;
    }

    public function seer_saisi(): ?float
    {
        return $this->eer_saisi() / 0.95;
    }
}
