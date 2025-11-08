<?php

namespace App\Legacy\Model;

use App\Domain\Common\ValueObject\Id;
use App\Legacy\Utils\Normalizer;

/**
 * @property array<MasqueLointainNonHomogene> $masque_lointain_non_homogene_collection
 */
final class BaieVitree extends Paroi
{
    private ?string $masque_proche_id = null;
    private ?string $masque_lointain_id = null;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_paroi,
        public readonly ?string $reference_lnc,
        public readonly ?string $description,
        public readonly ?float $surface_aiu,
        public readonly ?float $surface_aue,
        public readonly float $surface_totale_baie,
        public readonly int $nb_baie,
        public readonly bool $double_fenetre,
        public readonly ?float $epaisseur_lame,
        public readonly ?bool $vitrage_vir,
        public readonly ?bool $presence_protection_solaire_hors_fermeture,
        public readonly bool $presence_retour_isolation,
        public readonly bool $presence_joint,
        public readonly float $largeur_dormant,
        public readonly ?float $ug_saisi,
        public readonly ?float $uw_saisi,
        public readonly ?float $ujn_saisi,
        public readonly ?float $sw_saisi,

        public readonly int $enum_orientation_id,
        public readonly int $enum_type_pose_id,
        public readonly int $enum_type_vitrage_id,
        public readonly int $enum_inclinaison_vitrage_id,
        public readonly int $enum_type_baie_id,
        public readonly int $enum_type_materiaux_menuiserie_id,
        public readonly int $enum_type_fermeture_id,
        public readonly int $enum_methode_saisie_perf_vitrage_id,
        public readonly int $enum_type_adjacence_id,
        public readonly ?int $enum_cfg_isolation_lnc_id,
        public readonly ?int $enum_type_gaz_lame_id,

        public readonly int $tv_coef_masque_proche_id,
        public readonly ?int $tv_ug_id,
        public readonly ?int $tv_uw_id,
        public readonly ?int $tv_sw_id,
        public readonly ?int $tv_deltar_id,
        public readonly ?int $tv_ujn_id,
        public readonly ?int $tv_coef_masque_lointain_homogene_id,
        public readonly ?int $tv_coef_reduction_deperdition_id,

        public readonly float $b,
        public readonly ?float $uw_1,
        public readonly ?float $uw_2,
        public readonly ?float $sw_1,
        public readonly ?float $sw_2,
        public readonly ?float $ug,
        public readonly float $uw,
        public readonly ?float $ujn,
        public readonly float $u_menuiserie,
        public readonly float $sw,
        public readonly float $fe1,
        public readonly float $fe2,

        public readonly array $masque_lointain_non_homogene_collection,
        public readonly ?DoubleFenetre $baie_vitree_double_fenetre,
    ) {}

    /**
     * XSD //enveloppe/baie_vitree_collection/baie_vitree
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        $masque_lointain_non_homogene_collection = [];
        foreach ($xml->masque_lointain_non_homogene_collection->masque_lointain_non_homogene ?? [] as $item) {
            $masque_lointain_non_homogene_collection[] = MasqueLointainNonHomogene::from($item);
        }

        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            reference_paroi: Normalizer::referenceval((string) $xml->donnee_entree->reference_paroi),
            reference_lnc: Normalizer::referenceval((string) $xml->donnee_entree->reference_lnc),
            description: (string) $xml->donnee_entree->description ?: null,
            surface_aiu: (float) $xml->donnee_entree->surface_aiu ?: null,
            surface_aue: (float) $xml->donnee_entree->surface_aue ?: null,
            surface_totale_baie: (float) $xml->donnee_entree->surface_totale_baie,
            nb_baie: (int) $xml->donnee_entree->nb_baie,
            double_fenetre: (bool)(int) $xml->donnee_entree->double_fenetre,
            epaisseur_lame: (float) $xml->donnee_entree->epaisseur_lame ?: null,
            vitrage_vir: (bool)(int) $xml->donnee_entree->vitrage_vir ?: null,
            presence_protection_solaire_hors_fermeture: (bool)(int) $xml->donnee_entree->presence_protection_solaire_hors_fermeture ?: null,
            presence_retour_isolation: (bool)(int) $xml->donnee_entree->presence_retour_isolation,
            presence_joint: (bool)(int) $xml->donnee_entree->presence_joint,
            largeur_dormant: (float) $xml->donnee_entree->largeur_dormant,
            ug_saisi: (float) $xml->donnee_entree->ug_saisi ?: null,
            uw_saisi: (float) $xml->donnee_entree->uw_saisi ?: null,
            ujn_saisi: (float) $xml->donnee_entree->ujn_saisi ?: null,
            sw_saisi: (float) $xml->donnee_entree->sw_saisi ?: null,

            enum_methode_saisie_perf_vitrage_id: (int) $xml->donnee_entree->enum_methode_saisie_perf_vitrage_id,
            enum_type_baie_id: (int) $xml->donnee_entree->enum_type_baie_id,
            enum_type_materiaux_menuiserie_id: (int) $xml->donnee_entree->enum_type_materiaux_menuiserie_id,
            enum_type_fermeture_id: (int) $xml->donnee_entree->enum_type_fermeture_id,
            enum_orientation_id: (int) $xml->donnee_entree->enum_orientation_id,
            enum_type_pose_id: (int) $xml->donnee_entree->enum_type_pose_id,
            enum_type_vitrage_id: (int) $xml->donnee_entree->enum_type_vitrage_id,
            enum_inclinaison_vitrage_id: (int) $xml->donnee_entree->enum_inclinaison_vitrage_id,
            enum_type_adjacence_id: (int) $xml->donnee_entree->enum_type_adjacence_id,
            enum_cfg_isolation_lnc_id: (int) $xml->donnee_entree->enum_cfg_isolation_lnc_id ?: null,
            enum_type_gaz_lame_id: (int) $xml->donnee_entree->enum_type_gaz_lame_id ?: null,

            tv_ug_id: (int) $xml->donnee_entree->tv_ug_id ?: null,
            tv_uw_id: (int) $xml->donnee_entree->tv_uw_id ?: null,
            tv_sw_id: (int) $xml->donnee_entree->tv_sw_id ?: null,
            tv_deltar_id: (int) $xml->donnee_entree->tv_deltar_id ?: null,
            tv_ujn_id: (int) $xml->donnee_entree->tv_ujn_id ?: null,
            tv_coef_masque_proche_id: (int) $xml->donnee_entree->tv_coef_masque_proche_id,
            tv_coef_masque_lointain_homogene_id: (int) $xml->donnee_entree->tv_coef_masque_lointain_homogene_id ?: null,
            tv_coef_reduction_deperdition_id: (int) $xml->donnee_entree->tv_coef_reduction_deperdition_id ?: null,

            b: (float) $xml->donnee_intermediaire->b,
            uw_1: (float) $xml->donnee_entree->uw_1 ?: null,
            uw_2: (float) $xml->donnee_entree->uw_2 ?: null,
            sw_1: (float) $xml->donnee_entree->sw_1 ?: null,
            sw_2: (float) $xml->donnee_entree->sw_2 ?: null,
            ug: (float) $xml->donnee_intermediaire->ug ?: null,
            uw: (float) $xml->donnee_intermediaire->uw,
            ujn: (float) $xml->donnee_intermediaire->ujn ?: null,
            u_menuiserie: (float) $xml->donnee_intermediaire->u_menuiserie,
            sw: (float) $xml->donnee_intermediaire->sw,
            fe1: (float) $xml->donnee_intermediaire->fe1,
            fe2: (float) $xml->donnee_intermediaire->fe2,

            masque_lointain_non_homogene_collection: $masque_lointain_non_homogene_collection,
            baie_vitree_double_fenetre: $xml->donnee_entree->baie_vitree_double_fenetre
                ? DoubleFenetre::from($xml->donnee_entree->baie_vitree_double_fenetre)
                : null,
        );
    }

    public function masque_proche_id(): ?string
    {
        if (null === $this->masque_proche_id) {
            if (null === $this->tv_coef_masque_proche_id) {
                return $this->masque_proche_id = null;
            }
            if (19 === $this->tv_coef_masque_proche_id) {
                return $this->masque_proche_id = null;
            }
            $this->masque_proche_id = (string) Id::create();
        }
        return $this->masque_proche_id;
    }

    public function masque_lointain_id(): ?string
    {
        if (null === $this->masque_lointain_id && null !== $this->tv_coef_masque_lointain_homogene_id) {
            $this->masque_lointain_id = (string) Id::create();
        }
        return $this->masque_lointain_id;
    }

    public function surface(): float
    {
        return $this->surface_totale_baie;
    }

    public function u(): float
    {
        return $this->ujn ?? $this->uw;
    }
}
