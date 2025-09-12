<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Position\TypePose;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\TypeBaie;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\NatureGazLame;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\TypeVitrage;

final class XMLDoubleFenetre
{
    public function __construct(
        public readonly int $enum_type_pose_id,
        public readonly int $enum_type_vitrage_id,
        public readonly int $enum_inclinaison_vitrage_id,
        public readonly ?int $enum_type_gaz_lame_id,
        public readonly ?float $epaisseur_lame,
        public readonly ?bool $vitrage_vir,
        public readonly int $enum_type_baie_id,
        public readonly int $enum_type_materiaux_menuiserie_id,
        public readonly int $enum_methode_saisie_perf_vitrage_id,
        public readonly ?float $ug_saisi,
        public readonly ?float $uw_saisi,
        public readonly ?float $sw_saisi,
        public readonly ?int $tv_ug_id,
        public readonly ?int $tv_uw_id,
        public readonly ?int $tv_sw_id,

        public readonly ?float $ug,
        public readonly float $uw,
        public readonly float $sw
    ) {}

    /**
     * XSD logement/enveloppe/baie_vitree_collection/baie_vitree/donnee_entree/baie_vitree_double_fenetre
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            enum_type_pose_id: (int) $xml->donnee_entree->enum_type_pose_id,
            enum_type_vitrage_id: (int) $xml->donnee_entree->enum_type_vitrage_id,
            enum_inclinaison_vitrage_id: (int) $xml->donnee_entree->enum_inclinaison_vitrage_id,
            enum_type_gaz_lame_id: (int) $xml->donnee_entree->enum_type_gaz_lame_id ?: null,
            epaisseur_lame: (float) $xml->donnee_entree->epaisseur_lame ?: null,
            vitrage_vir: (bool)(int) $xml->donnee_entree->vitrage_vir ?: null,
            enum_type_baie_id: (int) $xml->donnee_entree->enum_type_baie_id,
            enum_type_materiaux_menuiserie_id: (int) $xml->donnee_entree->enum_type_materiaux_menuiserie_id,
            enum_methode_saisie_perf_vitrage_id: (int) $xml->donnee_entree->enum_methode_saisie_perf_vitrage_id,
            ug_saisi: (float) $xml->donnee_entree->ug_saisi ?: null,
            uw_saisi: (float) $xml->donnee_entree->uw_saisi ?: null,
            sw_saisi: (float) $xml->donnee_entree->sw_saisi ?: null,
            tv_ug_id: (int) $xml->donnee_entree->tv_ug_id ?: null,
            tv_uw_id: (int) $xml->donnee_entree->tv_uw_id ?: null,
            tv_sw_id: (int) $xml->donnee_entree->tv_sw_id ?: null,
            ug: (float) $xml->donnee_intermediaire->ug ?: null,
            uw: (float) $xml->donnee_intermediaire->uw,
            sw: (float) $xml->donnee_intermediaire->sw
        );
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
}
