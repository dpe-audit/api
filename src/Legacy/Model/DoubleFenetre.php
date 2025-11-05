<?php

namespace App\Legacy\Model;

final class DoubleFenetre
{
    use WithId;

    public function __construct(
        public readonly ?float $epaisseur_lame,
        public readonly ?bool $vitrage_vir,
        public readonly ?float $ug_saisi,
        public readonly ?float $uw_saisi,
        public readonly ?float $sw_saisi,

        public readonly int $enum_type_pose_id,
        public readonly int $enum_type_vitrage_id,
        public readonly int $enum_inclinaison_vitrage_id,
        public readonly int $enum_type_baie_id,
        public readonly int $enum_type_materiaux_menuiserie_id,
        public readonly int $enum_methode_saisie_perf_vitrage_id,
        public readonly ?int $enum_type_gaz_lame_id,

        public readonly ?int $tv_ug_id,
        public readonly ?int $tv_uw_id,
        public readonly ?int $tv_sw_id,

        public readonly ?float $ug,
        public readonly float $uw,
        public readonly float $sw
    ) {}

    /**
     * XPATH //baie_vitree/donnee_entree/baie_vitree_double_fenetre
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            epaisseur_lame: (float) $xml->donnee_entree->epaisseur_lame ?: null,
            vitrage_vir: (bool)(int) $xml->donnee_entree->vitrage_vir ?: null,
            ug_saisi: (float) $xml->donnee_entree->ug_saisi ?: null,
            uw_saisi: (float) $xml->donnee_entree->uw_saisi ?: null,
            sw_saisi: (float) $xml->donnee_entree->sw_saisi ?: null,

            enum_type_baie_id: (int) $xml->donnee_entree->enum_type_baie_id,
            enum_type_materiaux_menuiserie_id: (int) $xml->donnee_entree->enum_type_materiaux_menuiserie_id,
            enum_methode_saisie_perf_vitrage_id: (int) $xml->donnee_entree->enum_methode_saisie_perf_vitrage_id,
            enum_type_pose_id: (int) $xml->donnee_entree->enum_type_pose_id,
            enum_type_vitrage_id: (int) $xml->donnee_entree->enum_type_vitrage_id,
            enum_inclinaison_vitrage_id: (int) $xml->donnee_entree->enum_inclinaison_vitrage_id,
            enum_type_gaz_lame_id: (int) $xml->donnee_entree->enum_type_gaz_lame_id ?: null,
            tv_ug_id: (int) $xml->donnee_entree->tv_ug_id ?: null,
            tv_uw_id: (int) $xml->donnee_entree->tv_uw_id ?: null,
            tv_sw_id: (int) $xml->donnee_entree->tv_sw_id ?: null,

            ug: (float) $xml->donnee_intermediaire->ug ?: null,
            uw: (float) $xml->donnee_intermediaire->uw,
            sw: (float) $xml->donnee_intermediaire->sw
        );
    }
}
