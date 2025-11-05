<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class Ventilation
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_ventile,
        public readonly bool $plusieurs_facade_exposee,
        public readonly ?float $q4pa_conv_saisi,
        public readonly bool $ventilation_post_2012,
        public readonly ?string $ref_produit_ventilation,
        public readonly ?float $cle_repartition_ventilation,

        public readonly int $enum_methode_saisie_q4pa_conv_id,
        public readonly int $enum_type_ventilation_id,
        public readonly int $tv_debits_ventilation_id,
        public readonly ?int $tv_q4pa_conv_id,

        public readonly ?float $pvent_moy,
        public readonly float $q4pa_conv,
        public readonly float $conso_auxiliaire_ventilation,
        public readonly float $hperm,
        public readonly float $hvent
    ) {}

    /**
     * XPATH //ventilation_collection/ventilation
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            surface_ventile: (float) $xml->donnee_entree->surface_ventile,
            plusieurs_facade_exposee: (bool)(int) $xml->donnee_entree->plusieurs_facade_exposee,
            q4pa_conv_saisi: (float) $xml->donnee_entree->q4pa_conv_saisi ?: null,
            ventilation_post_2012: (bool)(int) $xml->donnee_entree->ventilation_post_2012,
            ref_produit_ventilation: (string) $xml->donnee_entree->ref_produit_ventilation ?: null,
            cle_repartition_ventilation: (float) $xml->donnee_entree->cle_repartition_ventilation ?: null,
            enum_methode_saisie_q4pa_conv_id: (int) $xml->donnee_entree->enum_methode_saisie_q4pa_conv_id,
            enum_type_ventilation_id: (int) $xml->donnee_entree->enum_type_ventilation_id,
            tv_debits_ventilation_id: (int) $xml->donnee_entree->tv_debits_ventilation_id,
            tv_q4pa_conv_id: (int) $xml->donnee_entree->tv_q4pa_conv_id ?: null,
            pvent_moy: (float) $xml->donnee_intermediaire->pvent_moy ?: null,
            q4pa_conv: (float) $xml->donnee_intermediaire->q4pa_conv,
            conso_auxiliaire_ventilation: (float) $xml->donnee_intermediaire->conso_auxiliaire_ventilation,
            hperm: (float) $xml->donnee_intermediaire->hperm,
            hvent: (float) $xml->donnee_intermediaire->hvent
        );
    }
}
