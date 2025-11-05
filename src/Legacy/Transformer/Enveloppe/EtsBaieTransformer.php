<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Lnc\Baie\{Materiau, Mitoyennete, TypeVitrage};
use App\Dto\Enveloppe\Lnc\Baie\{BaieDto, PositionDto};
use App\Legacy\Model\{Ets, EtsBaie};

final class EtsBaieTransformer
{
    private EtsBaie $ets_baie;
    private Ets $ets;

    public function surface(): float
    {
        return $this->ets_baie->surface_totale_baie;
    }

    public function type_vitrage(): TypeVitrage
    {
        return match ($this->ets->tv_coef_transparence_ets_id) {
            1 => TypeVitrage::POLYCARBONATE,
            2, 7, 12, 17 => TypeVitrage::SIMPLE_VITRAGE,
            3, 8, 13, 18 => TypeVitrage::DOUBLE_VITRAGE,
            4, 9, 14, 19 => TypeVitrage::DOUBLE_VITRAGE_FE,
            5, 10, 15, 20 => TypeVitrage::TRIPLE_VITRAGE,
            6, 11, 16, 21 => TypeVitrage::TRIPLE_VITRAGE_FE,
            default => TypeVitrage::SIMPLE_VITRAGE,
        };
    }

    public function materiau(): ?Materiau
    {
        return match ($this->ets->tv_coef_transparence_ets_id) {
            1 => Materiau::POLYCARBONATE,
            2, 3, 4, 5, 6 => Materiau::BOIS,
            7, 8, 9, 10, 11 => Materiau::PVC,
            12, 13, 14, 15, 16, 17, 18, 19, 20, 21 => Materiau::METAL,
            default => null,
        };
    }

    public function presence_rupteur_pont_thermique(): ?bool
    {
        return match ($this->ets->tv_coef_transparence_ets_id) {
            12, 13, 14, 15, 16 => true,
            17, 18, 19, 20, 21 => false,
            default => null,
        };
    }

    public function orientation(): float
    {
        return match ($this->ets_baie->enum_orientation_id) {
            1 => 180,
            2 => 0,
            3 => 90,
            4 => 270,
        };
    }

    public function inclinaison(): float
    {
        return match ($this->ets_baie->enum_inclinaison_vitrage_id) {
            1 => 15,
            2 => 50,
            3 => 90,
            4 => 0,
        };
    }

    public function __invoke(EtsBaie $ets_baie, Ets $ets): BaieDto
    {
        $this->ets_baie = $ets_baie;
        $this->ets = $ets;

        return new BaieDto(
            id: $ets_baie->id(),
            description: $ets_baie->description(),
            type_vitrage: $this->type_vitrage(),
            materiau: $this->materiau(),
            presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
            position: new PositionDto(
                mitoyennete: Mitoyennete::EXTERIEUR,
                surface: $this->surface(),
                orientation: $this->orientation(),
                inclinaison: $this->inclinaison(),
            )
        );
    }
}
