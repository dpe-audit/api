<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Position\TypePose;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\TypeBaie;
use App\Domain\Enveloppe\Baie\TypeFermeture;
use App\Domain\Enveloppe\Baie\Vitrage\NatureGazLame;
use App\Domain\Enveloppe\Baie\Vitrage\TypeVitrage;
use App\Domain\Enveloppe\Masque\ConfigurationMasque;

/**
 * @property array<XMLMasqueLointainNonHomogene> $masque_lointain_non_homogene_collection
 */
final class XMLBaieVitree extends XMLParoi
{
    private ?Id $masque_proche_id = null;
    private ?Id $masque_lointain_id = null;

    /**
     * @var array<int, Id>
     */
    private array $masque_lointain_id_collection = [];

    public function __construct(
        public readonly ?string $reference_paroi,
        public readonly float $surface_totale_baie,
        public readonly int $nb_baie,
        public readonly bool $double_fenetre,
        public readonly int $enum_orientation_id,
        public readonly int $enum_type_pose_id,
        public readonly int $enum_type_vitrage_id,
        public readonly int $enum_inclinaison_vitrage_id,
        public readonly ?int $enum_type_gaz_lame_id,
        public readonly ?float $epaisseur_lame,
        public readonly ?bool $vitrage_vir,
        public readonly int $enum_type_baie_id,
        public readonly int $enum_type_materiaux_menuiserie_id,
        public readonly int $enum_type_fermeture_id,
        public readonly ?bool $presence_protection_solaire_hors_fermeture,
        public readonly bool $presence_retour_isolation,
        public readonly bool $presence_joint,
        public readonly float $largeur_dormant,
        public readonly int $enum_methode_saisie_perf_vitrage_id,
        public readonly ?float $ug_saisi,
        public readonly ?float $uw_saisi,
        public readonly ?float $ujn_saisi,
        public readonly ?float $sw_saisi,
        public readonly ?int $tv_ug_id,
        public readonly ?int $tv_uw_id,
        public readonly ?int $tv_sw_id,
        public readonly ?int $tv_deltar_id,
        public readonly ?int $tv_ujn_id,
        public readonly int $tv_coef_masque_proche_id,
        public readonly ?int $tv_coef_masque_lointain_homogene_id,
        public readonly ?float $uw_1,
        public readonly ?float $uw_2,
        public readonly ?float $sw_1,
        public readonly ?float $sw_2,
        public readonly array $masque_lointain_non_homogene_collection,
        public readonly ?XMLDoubleFenetre $baie_vitree_double_fenetre,

        public readonly ?float $ug,
        public readonly float $uw,
        public readonly ?float $ujn,
        public readonly float $u_menuiserie,
        public readonly float $sw,
        public readonly float $fe1,
        public readonly float $fe2,
    ) {}

    /**
     * XSD logement/enveloppe/baie_vitree_collection/baie_vitree
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            reference_paroi: (string) $xml->donnee_entree->reference_paroi ?: null,
            surface_totale_baie: (float) $xml->donnee_entree->surface_totale_baie,
            nb_baie: (int) $xml->donnee_entree->nb_baie,
            double_fenetre: (bool)(int) $xml->donnee_entree->double_fenetre,
            enum_orientation_id: (int) $xml->donnee_entree->enum_orientation_id,
            enum_type_pose_id: (int) $xml->donnee_entree->enum_type_pose_id,
            enum_type_vitrage_id: (int) $xml->donnee_entree->enum_type_vitrage_id,
            enum_inclinaison_vitrage_id: (int) $xml->donnee_entree->enum_inclinaison_vitrage_id,
            enum_type_gaz_lame_id: (int) $xml->donnee_entree->enum_type_gaz_lame_id ?: null,
            epaisseur_lame: (float) $xml->donnee_entree->epaisseur_lame ?: null,
            vitrage_vir: (bool)(int) $xml->donnee_entree->vitrage_vir ?: null,
            enum_type_baie_id: (int) $xml->donnee_entree->enum_type_baie_id,
            enum_type_materiaux_menuiserie_id: (int) $xml->donnee_entree->enum_type_materiaux_menuiserie_id,
            enum_type_fermeture_id: (int) $xml->donnee_entree->enum_type_fermeture_id,
            presence_protection_solaire_hors_fermeture: (bool)(int) $xml->donnee_entree->presence_protection_solaire_hors_fermeture ?: null,
            presence_retour_isolation: (bool)(int) $xml->donnee_entree->presence_retour_isolation,
            presence_joint: (bool)(int) $xml->donnee_entree->presence_joint,
            largeur_dormant: (float) $xml->donnee_entree->largeur_dormant,
            enum_methode_saisie_perf_vitrage_id: (int) $xml->donnee_entree->enum_methode_saisie_perf_vitrage_id,
            ug_saisi: (float) $xml->donnee_entree->ug_saisi ?: null,
            uw_saisi: (float) $xml->donnee_entree->uw_saisi ?: null,
            ujn_saisi: (float) $xml->donnee_entree->ujn_saisi ?: null,
            sw_saisi: (float) $xml->donnee_entree->sw_saisi ?: null,
            tv_ug_id: (int) $xml->donnee_entree->tv_ug_id ?: null,
            tv_uw_id: (int) $xml->donnee_entree->tv_uw_id ?: null,
            tv_sw_id: (int) $xml->donnee_entree->tv_sw_id ?: null,
            tv_deltar_id: (int) $xml->donnee_entree->tv_deltar_id ?: null,
            tv_ujn_id: (int) $xml->donnee_entree->tv_ujn_id ?: null,
            tv_coef_masque_proche_id: (int) $xml->donnee_entree->tv_coef_masque_proche_id,
            tv_coef_masque_lointain_homogene_id: (int) $xml->donnee_entree->tv_coef_masque_lointain_homogene_id ?: null,
            uw_1: (float) $xml->donnee_entree->uw_1 ?: null,
            uw_2: (float) $xml->donnee_entree->uw_2 ?: null,
            sw_1: (float) $xml->donnee_entree->sw_1 ?: null,
            sw_2: (float) $xml->donnee_entree->sw_2 ?: null,
            masque_lointain_non_homogene_collection: XMLMasqueLointainNonHomogene::from_collection($xml->masque_lointain_non_homogene_collection),
            baie_vitree_double_fenetre: $xml->donnee_entree->double_fenetre ? XMLDoubleFenetre::from($xml->donnee_entree->double_fenetre) : null,
            ug: (float) $xml->donnee_intermediaire->ug ?: null,
            uw: (float) $xml->donnee_intermediaire->uw,
            ujn: (float) $xml->donnee_intermediaire->ujn ?: null,
            u_menuiserie: (float) $xml->donnee_intermediaire->u_menuiserie,
            sw: (float) $xml->donnee_intermediaire->sw,
            fe1: (float) $xml->donnee_intermediaire->fe1,
            fe2: (float) $xml->donnee_intermediaire->fe2
        ))->set($xml);
    }

    /**
     * XSD logement/enveloppe/baie_vitree_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->baie_vitree as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    public function surface(): float
    {
        return $this->surface_totale_baie;
    }

    public function description(): string
    {
        return 'Description non renseignée';
    }

    public function type_baie(): TypeBaie
    {
        return match ($this->enum_type_baie_id) {
            1 => TypeBaie::BRIQUE_VERRE_PLEINE,
            2 => TypeBaie::BRIQUE_VERRE_CREUSE,
            3 => TypeBaie::POLYCARBONATE,
            4 => TypeBaie::FENETRE_BATTANTE,
            5 => TypeBaie::FENETRE_COULISSANTE,
            6 => TypeBaie::PORTE_FENETRE_COULISSANTE,
            7 => TypeBaie::PORTE_FENETRE_BATTANTE,
            8 => TypeBaie::PORTE_FENETRE_BATTANTE,
        };
    }

    public function type_pose(): ?TypePose
    {
        return match ($this->enum_type_pose_id) {
            1 => TypePose::NU_EXTERIEUR,
            2 => TypePose::NU_INTERIEUR,
            3 => TypePose::TUNNEL,
            4 => null,
        };
    }

    public function orientation(): ?float
    {
        return match ($this->enum_orientation_id) {
            1 => 180,
            2 => 0,
            3 => 90,
            4 => 270,
            default => null,
        };
    }

    public function inclinaison(): float
    {
        return match ($this->enum_inclinaison_vitrage_id) {
            1 => 15,
            2 => 50,
            3 => 90,
            4 => 0,
        };
    }

    public function presence_soubassement(): ?bool
    {
        return match ($this->enum_type_baie_id) {
            7 => true,
            8 => false,
            default => null,
        };
    }

    public function materiau(): ?Materiau
    {
        return match ($this->enum_type_materiaux_menuiserie_id) {
            3 => Materiau::BOIS,
            4 => Materiau::BOIS_METAL,
            5 => Materiau::PVC,
            6 => Materiau::METAL,
            7 => Materiau::METAL,
            default => null,
        };
    }

    public function type_vitrage(): TypeVitrage
    {
        return match ($this->enum_type_vitrage_id) {
            1, 4 => TypeVitrage::SIMPLE_VITRAGE,
            2 => $this->vitrage_vir ? TypeVitrage::DOUBLE_VITRAGE_FE : TypeVitrage::DOUBLE_VITRAGE,
            3 => $this->vitrage_vir ? TypeVitrage::TRIPLE_VITRAGE_FE : TypeVitrage::TRIPLE_VITRAGE,
            5 => TypeVitrage::BRIQUE_VERRE,
            6 => TypeVitrage::POLYCARBONATE,
        };
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        return match ($this->enum_type_vitrage_id) {
            4 => $this->vitrage_vir ? TypeSurvitrage::SURVITRAGE_FE : TypeSurvitrage::SURVITRAGE_SIMPLE,
            1, 2, 3, 5, 6 => null,
        };
    }

    public function nature_lame(): ?NatureGazLame
    {
        return match ($this->enum_type_gaz_lame_id) {
            1 => NatureGazLame::AIR,
            2 => NatureGazLame::ARGON,
            default => null,
        };
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->enum_type_materiaux_menuiserie_id === 6 ? true : false;
    }

    public function type_fermeture(): TypeFermeture
    {
        return match ($this->enum_type_fermeture_id) {
            1 => TypeFermeture::SANS_FERMETURE,
            2 => TypeFermeture::VOLET_BATTANT_AVEC_AJOURS_FIXES,
            3 => TypeFermeture::FERMETURE_LAMES_ORIENTABLES,
            4 => TypeFermeture::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_LTE_12MM,
            5 => TypeFermeture::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_LTE_22MM,
            6 => TypeFermeture::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_GT_12MM,
            7 => TypeFermeture::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_GT_22MM,
            8 => TypeFermeture::FERMETURE_ISOLEE_SANS_AJOURS,
        };
    }

    public function presence_protection_solaire(): bool
    {
        return $this->presence_protection_solaire_hors_fermeture ?? false;
    }

    public function largeur_dormant(): int
    {
        return $this->largeur_dormant * 10;
    }

    public function paroi_id(XMLRessource $xml): ?Id
    {
        if ($this->reference_paroi) {
            return $xml->logement()->enveloppe->find_mur($this->reference_paroi)?->id()
                ?? $xml->logement()->enveloppe->find_plancher_haut($this->reference_paroi)?->id()
                ?? $xml->logement()->enveloppe->find_plancher_bas($this->reference_paroi)?->id()
                ?? null;
        }
        return null;
    }

    public function double_fenetre_id(): ?Id
    {
        return $this->baie_vitree_double_fenetre?->id();
    }

    public function configuration_masque_proche(): ?ConfigurationMasque
    {
        return match ($this->tv_coef_masque_proche_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => ConfigurationMasque::FOND_BALCON,
            13, 14, 15, 16 => ConfigurationMasque::BALCON_OU_AUVENT,
            17 => ConfigurationMasque::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD,
            18 => ConfigurationMasque::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD,
            default => null,
        };
    }

    public function orientation_masque_proche(): ?Orientation
    {
        return match ($this->tv_coef_masque_proche_id) {
            1, 2, 3, 4 => Orientation::NORD,
            5, 6, 7, 8 => Orientation::SUD,
            9, 10, 11, 12 => Orientation::EST,
            default => null,
        };
    }

    public function orientation_masque_lointain(): ?Orientation
    {
        return match ($this->tv_coef_masque_lointain_homogene_id) {
            1, 2, 3, 4 => Orientation::NORD,
            5, 6, 7, 8 => Orientation::SUD,
            9, 10, 11, 12 => Orientation::EST,
            default => null,
        };
    }

    public function profondeur_masque_proche(): ?float
    {
        return match ($this->tv_coef_masque_proche_id) {
            1, 5, 9, 13 => 0.5,
            2, 6, 10, 14 => 1.5,
            3, 7, 11, 15 => 2.5,
            4, 8, 12, 16 => 3.5,
            default => null,
        };
    }

    public function hauteur_masque_lointain(): ?float
    {
        return match ($this->tv_coef_masque_lointain_homogene_id) {
            1, 5, 9 => 7.5,
            2, 6, 10 => 22.5,
            3, 7, 11 => 45,
            4, 8, 12 => 75,
            default => null,
        };
    }

    public function configuration_masque_lointain(): ?ConfigurationMasque
    {
        return $this->tv_coef_masque_lointain_homogene_id
            ? ConfigurationMasque::HOMOGENE
            : null;
    }

    public function masque_proche_id(): ?Id
    {
        return $this->configuration_masque_proche()
            ? $this->masque_proche_id ??= Id::create()
            : null;
    }

    public function masque_lointain_homogene_id(): ?Id
    {
        return $this->configuration_masque_lointain()
            ? $this->masque_lointain_id ??= Id::create()
            : null;
    }

    public function masque_lointain_non_homogene_id(int $index): ?Id
    {
        return array_key_exists($index, $this->masque_lointain_non_homogene_collection)
            ? $this->masque_lointain_id_collection[$index] ??= Id::create()
            : null;
    }
}
