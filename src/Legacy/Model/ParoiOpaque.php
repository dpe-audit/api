<?php

namespace App\Legacy\Model;

abstract class ParoiOpaque extends Paroi
{
    public readonly float $surface_paroi_opaque;
    public readonly ?float $resistance_isolation;
    public readonly ?float $epaisseur_isolation;
    public readonly ?bool $paroi_lourde;
    public readonly int $enum_methode_saisie_u0_id;
    public readonly int $enum_methode_saisie_u_id;
    public readonly int $enum_type_isolation_id;
    public readonly ?int $enum_periode_isolation_id;

    public function surface(): float
    {
        return $this->surface_paroi_opaque;
    }
}
