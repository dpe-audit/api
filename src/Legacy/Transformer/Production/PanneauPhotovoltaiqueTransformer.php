<?php

namespace App\Legacy\Transformer\Production;

use App\Dto\Production\PanneauPhotovoltaiqueDto;
use App\Legacy\Model\PanneauPv;
use App\Legacy\Transformer\Context;

final class PanneauPhotovoltaiqueTransformer
{
    private PanneauPv $panneau_pv;

    public function orientation(): ?float
    {
        return match ($this->panneau_pv->enum_orientation_pv_id) {
            1 => 180,
            2 => 0,
            3 => 90,
            4 => 270,
            default => match ($this->panneau_pv->tv_coef_orientation_pv_id) {
                1, 6, 11, 16 => 90,
                2, 7, 12, 17 => 135,
                3, 8, 13, 18 => 180,
                4, 9, 14, 19 => 225,
                5, 10, 15, 20 => 270,
                default => null,
            },
        };
    }

    public function inclinaison(): float
    {
        return match ($this->panneau_pv->enum_inclinaison_pv_id) {
            1 => 10,
            2 => 30,
            3 => 60,
            4 => 80,
            default => match ($this->panneau_pv->tv_coef_orientation_pv_id) {
                1, 2, 3, 4, 5 => 10,
                6, 7, 8, 9, 10 => 30,
                11, 12, 13, 14, 15 => 60,
                16, 17, 18, 19, 20 => 80,
                default => null,
            },
        };
    }

    public function installation_collective(): bool
    {
        return $this->panneau_pv->ratio_virtualisation > 0;
    }

    public function modules(): int
    {
        return $this->panneau_pv->nombre_module ?? $this->panneau_pv->surface_totale_capteurs > 0 ? 1 : 0;
    }

    public function surface(): ?float
    {
        return $this->panneau_pv->surface_totale_capteurs > 0 ? $this->panneau_pv->surface_totale_capteurs : null;
    }

    public function __invoke(PanneauPv $panneau_pv, Context $context): ?PanneauPhotovoltaiqueDto
    {
        $this->panneau_pv = $panneau_pv;

        if (null === $orientation = $this->orientation()) {
            return null;
        }
        if (null === $inclinaison = $this->inclinaison()) {
            return null;
        }
        if (0 === $modules = $this->modules()) {
            return null;
        }
        return new PanneauPhotovoltaiqueDto(
            id: $panneau_pv->id(),
            description: "Non renseigné",
            orientation: $orientation,
            inclinaison: $inclinaison,
            modules: $modules,
            surface: $this->surface(),
            installation_collective: $this->installation_collective(),
        );
    }
}
