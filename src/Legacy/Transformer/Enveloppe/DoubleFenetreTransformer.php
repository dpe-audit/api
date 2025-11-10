<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\DoubleFenetre\TypeBaie;
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Position\TypePose;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\{NatureGazLame, TypeVitrage};
use App\Dto\Enveloppe\DoubleFenetre\{DoubleFenetreDto, MenuiserieDto, PositionDto, SurvitrageDto, VitrageDto};
use App\Legacy\Model\DoubleFenetre;

final class DoubleFenetreTransformer
{
    private DoubleFenetre $double_fenetre;

    public function type_baie(): TypeBaie
    {
        return match ($this->double_fenetre->enum_type_baie_id) {
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
        return match ($this->double_fenetre->enum_type_pose_id) {
            1 => TypePose::NU_EXTERIEUR,
            2 => TypePose::NU_INTERIEUR,
            3 => TypePose::TUNNEL,
            4 => null,
        };
    }

    public function inclinaison(): float
    {
        return match ($this->double_fenetre->enum_inclinaison_vitrage_id) {
            1 => 15,
            2 => 50,
            3 => 90,
            4 => 0,
        };
    }

    public function presence_soubassement(): ?bool
    {
        return match ($this->double_fenetre->enum_type_baie_id) {
            7 => true,
            8 => false,
            default => null,
        };
    }

    public function materiau(): ?Materiau
    {
        return match ($this->double_fenetre->enum_type_materiaux_menuiserie_id) {
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
        if ($this->type_baie()->is_paroi_brique_verre()) {
            return TypeVitrage::BRIQUE_VERRE;
        }
        if ($this->type_baie()->is_paroi_polycarbonate()) {
            return TypeVitrage::POLYCARBONATE;
        }
        return match ($this->double_fenetre->enum_type_vitrage_id) {
            1, 4 => TypeVitrage::SIMPLE_VITRAGE,
            2 => $this->double_fenetre->vitrage_vir ? TypeVitrage::DOUBLE_VITRAGE_FE : TypeVitrage::DOUBLE_VITRAGE,
            3 => $this->double_fenetre->vitrage_vir ? TypeVitrage::TRIPLE_VITRAGE_FE : TypeVitrage::TRIPLE_VITRAGE,
            5 => TypeVitrage::BRIQUE_VERRE,
            6 => TypeVitrage::POLYCARBONATE,
        };
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        return match ($this->double_fenetre->enum_type_vitrage_id) {
            4 => $this->double_fenetre->vitrage_vir ? TypeSurvitrage::SURVITRAGE_FE : TypeSurvitrage::SURVITRAGE_SIMPLE,
            1, 2, 3, 5, 6 => null,
        };
    }

    public function nature_lame(): ?NatureGazLame
    {
        return match ($this->double_fenetre->enum_type_gaz_lame_id) {
            1 => NatureGazLame::AIR,
            2 => NatureGazLame::ARGON,
            default => null,
        };
    }

    public function epaisseur_lame(): ?float
    {
        if (false === $this->type_vitrage()->vitrage_complexe()) {
            return null;
        }
        return $this->double_fenetre->epaisseur_lame > 0 ? $this->double_fenetre->epaisseur_lame : null;
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->double_fenetre->enum_type_materiaux_menuiserie_id === 6 ? true : false;
    }

    public function ug(): ?float
    {
        return $this->double_fenetre->ug_saisi > 0 ? $this->double_fenetre->ug_saisi : null;
    }

    public function uw(): ?float
    {
        return $this->double_fenetre->uw_saisi > 0 ? $this->double_fenetre->uw_saisi : null;
    }

    public function sw(): ?float
    {
        return $this->double_fenetre->sw_saisi > 0 ? $this->double_fenetre->sw_saisi : null;
    }

    public function __invoke(DoubleFenetre $double_fenetre): DoubleFenetreDto
    {
        $this->double_fenetre = $double_fenetre;

        return new DoubleFenetreDto(
            id: $double_fenetre->id(),
            description: "Double fenêtre",
            type: $this->type_baie(),
            ug: $this->ug(),
            uw: $this->uw(),
            sw: $this->sw(),
            position: new PositionDto(
                inclinaison: $this->inclinaison(),
                type_pose: $this->type_pose(),
                presence_soubassement: $this->presence_soubassement()
            ),
            vitrage: new VitrageDto(
                type: $this->type_vitrage(),
                nature_lame: $this->nature_lame(),
                epaisseur_lame: $this->epaisseur_lame(),
            ),
            survitrage: $this->type_survitrage() ? new SurvitrageDto(
                type: $this->type_survitrage(),
                epaisseur_lame: null,
            ) : null,
            menuiserie: $this->type_baie()->is_paroi_vitree() ? null : new MenuiserieDto(
                materiau: $this->materiau(),
                largeur_dormant: null,
                presence_joint: null,
                presence_retour_isolation: null,
                presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
            )
        );
    }
}
