<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class GenerateurChauffage
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_generateur_mixte,
        public readonly ?string $description,
        public readonly ?string $ref_produit_generateur_ch,
        public readonly bool $position_volume_chauffe,
        public readonly ?string $identifiant_reseau_chaleur,
        public readonly ?string $date_arrete_reseau_chaleur,
        public readonly ?int $n_radiateurs_gaz,
        public readonly ?int $priorite_generateur_cascade,
        public readonly ?bool $presence_ventouse,
        public readonly ?bool $presence_regulation_combustion,

        public readonly int $enum_type_generateur_ch_id,
        public readonly int $enum_usage_generateur_id,
        public readonly int $enum_type_energie_id,
        public readonly int $enum_methode_saisie_carac_sys_id,
        public readonly int $enum_lien_generateur_emetteur_id,
        public readonly ?int $tv_rendement_generation_id,
        public readonly ?int $tv_scop_id,
        public readonly ?int $tv_temp_fonc_100_id,
        public readonly ?int $tv_temp_fonc_30_id,
        public readonly ?int $tv_generateur_combustion_id,
        public readonly ?int $tv_reseau_chaleur_id,

        public readonly ?float $scop,
        public readonly ?float $pn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $temp_fonc_30,
        public readonly ?float $temp_fonc_100,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $rendement_generation,
        public readonly float $conso_ch,
        public readonly float $conso_ch_depensier
    ) {}

    /**
     * XPATH //installation_chauffage/generateur_chauffage_collection/generateur_chauffage
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            reference_generateur_mixte: Normalizer::referenceval((string) $xml->donnee_entree->reference_generateur_mixte),
            description: (string) $xml->donnee_entree->description ?: null,
            ref_produit_generateur_ch: (string) $xml->donnee_entree->ref_produit_generateur_ch ?: null,
            position_volume_chauffe: (bool)(int) $xml->donnee_entree->position_volume_chauffe,
            identifiant_reseau_chaleur: (string) $xml->donnee_entree->identifiant_reseau_chaleur ?: null,
            date_arrete_reseau_chaleur: (string) $xml->donnee_entree->date_arrete_reseau_chaleur ?: null,
            n_radiateurs_gaz: (int) $xml->donnee_entree->n_radiateurs_gaz ?: null,
            priorite_generateur_cascade: (int) $xml->donnee_entree->priorite_generateur_cascade ?: null,
            presence_ventouse: (bool)(int) $xml->donnee_entree->presence_ventouse ?: null,
            presence_regulation_combustion: (bool)(int) $xml->donnee_entree->presence_regulation_combustion ?: null,

            enum_type_generateur_ch_id: (int) $xml->donnee_entree->enum_type_generateur_ch_id,
            enum_usage_generateur_id: (int) $xml->donnee_entree->enum_usage_generateur_id,
            enum_type_energie_id: (int) $xml->donnee_entree->enum_type_energie_id,
            enum_methode_saisie_carac_sys_id: (int) $xml->donnee_entree->enum_methode_saisie_carac_sys_id,
            enum_lien_generateur_emetteur_id: (int) $xml->donnee_entree->enum_lien_generateur_emetteur_id,
            tv_rendement_generation_id: (int) $xml->donnee_entree->tv_rendement_generation_id ?: null,
            tv_scop_id: (int) $xml->donnee_entree->tv_scop_id ?: null,
            tv_temp_fonc_100_id: (int) $xml->donnee_entree->tv_temp_fonc_100_id ?: null,
            tv_temp_fonc_30_id: (int) $xml->donnee_entree->tv_temp_fonc_30_id ?: null,
            tv_generateur_combustion_id: (int) $xml->donnee_entree->tv_generateur_combustion_id ?: null,
            tv_reseau_chaleur_id: (int) $xml->donnee_entree->tv_reseau_chaleur_id ?: null,

            scop: (float) $xml->donnee_intermediaire->scop ?: null,
            pn: (float) $xml->donnee_intermediaire->pn ?: null,
            qp0: (float) $xml->donnee_intermediaire->qp0 ?: null,
            pveilleuse: (float) $xml->donnee_intermediaire->pveilleuse ?: null,
            temp_fonc_30: (float) $xml->donnee_intermediaire->temp_fonc_30 ?: null,
            temp_fonc_100: (float) $xml->donnee_intermediaire->temp_fonc_100 ?: null,
            rpn: (float) $xml->donnee_intermediaire->rpn ?: null,
            rpint: (float) $xml->donnee_intermediaire->rpint ?: null,
            rendement_generation: (float) $xml->donnee_intermediaire->rendement_generation ?: null,
            conso_ch: (float) $xml->donnee_intermediaire->conso_ch,
            conso_ch_depensier: (float) $xml->donnee_intermediaire->conso_ch_depensier
        );
    }

    public function match(string $reference): bool
    {
        return $this->reference === $reference || str_contains($this->reference, $reference);
    }

    public function match_generateur_ecs(GenerateurEcs $generateur_ecs): bool
    {
        return $generateur_ecs->enum_type_generateur_ecs_id === $this->enum_type_generateur_ecs_id()
            && $generateur_ecs->enum_type_energie_id === $this->enum_type_energie_id;
    }

    public function enum_type_generateur_ecs_id(): ?int
    {
        return match ($this->enum_type_generateur_ch_id) {
            48 => 13,
            49 => 14,
            55 => 15,
            56 => 16,
            57 => 17,
            58 => 18,
            59 => 19,
            60 => 20,
            61 => 21,
            62 => 22,
            63 => 23,
            64 => 24,
            65 => 25,
            66 => 26,
            67 => 27,
            68 => 28,
            69 => 29,
            70 => 30,
            71 => 31,
            72 => 32,
            73 => 33,
            74 => 34,
            75 => 35,
            76 => 36,
            77 => 37,
            78 => 38,
            79 => 39,
            80 => 40,
            81 => 41,
            82 => 42,
            83 => 43,
            84 => 44,
            85 => 45,
            86 => 46,
            87 => 47,
            88 => 48,
            89 => 49,
            90 => 50,
            91 => 51,
            92 => 52,
            93 => 53,
            94 => 54,
            95 => 55,
            96 => 56,
            97 => 57,
            113 => 78,
            114 => 79,
            115 => 80,
            116 => 81,
            117 => 82,
            118 => 83,
            119 => 84,
            120 => 85,
            121 => 86,
            122 => 87,
            123 => 88,
            124 => 89,
            125 => 90,
            126 => 91,
            127 => 92,
            128 => 93,
            129 => 94,
            130 => 95,
            131 => 96,
            132 => 97,
            133 => 98,
            134 => 99,
            135 => 100,
            136 => 101,
            137 => 102,
            138 => 103,
            139 => 104,
            140 => 115,
            141 => 116,
            148 => 120,
            149 => 121,
            150 => 122,
            151 => 123,
            152 => 124,
            153 => 125,
            154 => 126,
            155 => 127,
            156 => 128,
            157 => 129,
            158 => 130,
            159 => 131,
            160 => 132,
            161 => 133,
            default => null,
        };
    }

    public function match_generateur_hybride(self $generateur_chauffage): bool
    {
        if ($this->enum_type_generateur_ch_id <= 148) {
            return false;
        }
        if ($this->enum_type_generateur_ch_id >= 161) {
            return false;
        }
        if ($this->enum_lien_generateur_emetteur_id !== $generateur_chauffage->enum_lien_generateur_emetteur_id) {
            return false;
        }
        return true;
    }

    public function pn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->pn,
            default => null,
        };
    }

    public function rpn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->rpn,
            default => null,
        };
    }

    public function rpint_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->rpint,
            default => null,
        };
    }

    public function qp0_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->qp0,
            default => null,
        };
    }

    public function tfonc30_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->temp_fonc_30,
            default => null,
        };
    }

    public function tfonc100_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->temp_fonc_100,
            default => null,
        };
    }

    public function pveilleuse_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->pveilleuse,
            default => null,
        };
    }

    public function scop_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->scop,
            default => null,
        };
    }
}
